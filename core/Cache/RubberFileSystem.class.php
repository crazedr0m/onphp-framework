<?php
/***************************************************************************
 *   Copyright (C) 2005-2008 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * Simple filesystem cache.
	 * 
	 * @ingroup Cache
	**/
	final class RubberFileSystem extends CachePeer
	{
		private $directory	= null;
		
		/**
		 * @return RubberFileSystem
		**/
		public static function create($directory = 'cache/')
		{
			return new self($directory);
		}

		public function __construct($directory = 'cache/')
		{
			$directory = ONPHP_TEMP_PATH.$directory;
			
			if (!is_writable($directory)) {
				if (!mkdir($directory, 0700, true)) {
					throw new WrongArgumentException(
						"can not write to '{$directory}'"
					);
				}
			}
			
			if ($directory[strlen($directory) - 1] != DIRECTORY_SEPARATOR)
				$directory .= DIRECTORY_SEPARATOR;
			
			$this->directory = $directory;
		}
		
		public function isAlive()
		{
			if (!is_writable($this->directory))
				return mkdir($this->directory, 0700, true);
			else
				return true;
		}
		
		/**
		 * @return RubberFileSystem
		**/
		public function clean()
		{
			// just to return 'true'
			FileUtils::removeDirectory($this->directory, true);
			
			return parent::clean();
		}
		
		public function increment($key, $value)
		{
			$path = $this->makePath($key);
			
			if (null !== ($current = $this->operate($path))) {
				$this->operate($path, $current += $value);
				
				return $current;
			}
			
			return null;
		}
		
		public function decrement($key, $value)
		{
			$path = $this->makePath($key);
			
			if (null !== ($current = $this->operate($path))) {
				$this->operate($path, $current -= $value);
				
				return $current;
			}
			
			return null;
		}
		
		public function get($key)
		{
			$path = $this->makePath($key);
			
			if (!is_readable($path)) {
				return null;
			}

			try {
				$fileTime = filemtime($path);
			} catch (BaseException $e) {
				$fileTime = null;
			}


			if ($fileTime === null) {
				return null;
			}

			if ($fileTime <= time()) {
				try {
					unlink($path);
				} catch (BaseException $e) {
					// we're in race with unexpected clean()
				}
				return null;
			}

			return $this->operate($path);
		}
		
		public function delete($key)
		{
			try {
				unlink($this->makePath($key));
			} catch (BaseException $e) {
				return false;
			}
			
			return true;
		}
		
		public function append($key, $data)
		{
			$path = $this->makePath($key);
			
			$directory = dirname($path);
			
			if (!file_exists($directory)) {
				try {
					mkdir($directory);
				} catch (BaseException $e) {
					// we're in race
				}
			}
			
			if (!is_writable($path))
				return false;
			
			try {
				$fp = fopen($path, 'ab');
			} catch (BaseException $e) {
				return false;
			}
			
			fwrite($fp, $data);
			
			fclose($fp);
			
			return true;
		}
		
		protected function store($action, $key, $value, $expires = 0)
		{
			$path = $this->makePath($key);
			$time = time();
			
			$directory = dirname($path);
			
			if (!file_exists($directory)) {
				try {
					mkdir($directory);
				} catch (BaseException $e) {
					// we're in race
				}
			}
			
			// do not add, if file exist and not expired
			if (
				$action == 'add'
				&& is_readable($path)
				&& filemtime($path) > $time
			)
				return true;
			
			// do not replace, when file not exist or expired
			if ($action == 'replace') {
				
				if (!is_readable($path)) {
					return false;
				} elseif (filemtime($path) <= $time) {
					$this->delete($key);
					return false;
				}
			}
			
			$this->operate($path, $value, $expires);
			
			return true;
		}
		
		private function operate($path, $value = null, $expires = null)
		{
			$key = hexdec(substr(md5($path), 3, 2)) + 1;

			$pool = SemaphorePool::me();

			if (!$pool->get($key)) {
				return null;
			}

			$result = $this->doOperate($path, $value, $expires);
			$free = $pool->drop($key);
			if ($value !== null) {
				$result = $free;
			}
			return $result;
		}

		private function doOperate($path, $value = null, $expires = null)
		{
			$tmp = null;
			try {
				if ($value === null) {
					$fp = fopen($path, 'rb');
				} else {
					$old = umask(0077);
					$tmp = FileUtils::makeTempFile();
					$fp = fopen($tmp, 'wb');
					umask($old);
				}
			} catch (BaseException $e) {
				fclose($fp);
				if ($tmp) {
					unlink($tmp);
				}
				return null;
			}

			if ($value === null) {
				$size = filesize($path);

				if ($size > 0)
					$data = fread($fp, $size);
				else
					$data = null;

				fclose($fp);

				return $data ? $this->restoreData($data) : null;
			}

			fwrite($fp, $this->prepareData($value));
			fflush($fp);
			fclose($fp);
			rename($tmp, $path);

			if ($expires < parent::TIME_SWITCH)
				$expires += time();

			try {
				touch($path, $expires);
			} catch (BaseException $e) {
				// race-removed
			}

			return;
		}

		private function makePath($key)
		{
			return
				$this->directory
				.$key[0].$key[1]
				.DIRECTORY_SEPARATOR
				.substr($key, 2);
		}
	}
?>
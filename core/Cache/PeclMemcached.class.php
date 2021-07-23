<?php
/***************************************************************************
 *   Copyright (C) 2006-2012 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * Connector for PECL's Memcache extension by Antony Dovgal.
	 *
	 * @see http://tony2001.phpclub.net/
	 * @see http://pecl.php.net/package/memcache
	 *
	 * @ingroup Cache
	**/
	class PeclMemcached extends CachePeer
	{
		const DEFAULT_PORT		= 11211;
		const DEFAULT_HOST		= '127.0.0.1';
		const DEFAULT_TIMEOUT	= 1;
		
		protected $host			= null;
		protected $port			= null;

		/**
		 * @var Memcached
		**/
		private $instance		= null;
		private $requestTimeout = null;
		private $connectTimeout = null;
		private $triedConnect	= false;
		private $persistentId   = 'persist';
		
		/**
		 * @return PeclMemcached
		**/
		public static function create(
			$host = self::DEFAULT_HOST,
			$port = self::DEFAULT_PORT,
			$connectTimeout = self::DEFAULT_TIMEOUT
		)
		{
			return new self($host, $port, $connectTimeout);
		}
		
		public function __construct(
			$host = self::DEFAULT_HOST,
			$port = self::DEFAULT_PORT,
			$connectTimeout = self::DEFAULT_TIMEOUT
		)
		{
			$this->host = $host;
			$this->port = $port;
			$this->connectTimeout = $connectTimeout;
		}
		
		public function __destruct()
		{
			if (!$this->alive) {
				return;
			}

			try {
				$this->instance->quit();
			} catch (Exception $e) {
				// shhhh.
			}
		}

		public function setPersistentId($id)
		{
			$this->persistentId = $id;
		}

		public function isAlive()
		{
			$this->ensureTriedToConnect();
			
			return parent::isAlive();
		}
		
		/**
		 * @return PeclMemcached
		**/
		public function clean()
		{
			$this->ensureTriedToConnect();
			
			try {
				$this->instance->flush();
			} catch (Exception $e) {
				$this->alive = false;
			}
			
			return parent::clean();
		}
		
		public function increment($key, $value)
		{
			$this->ensureTriedToConnect();
			
			try {
				return $this->instance->increment($key, $value);
			} catch (Exception $e) {
				return null;
			}
		}
		
		public function decrement($key, $value)
		{
			$this->ensureTriedToConnect();
			
			try {
				return $this->instance->decrement($key, $value);
			} catch (Exception $e) {
				return null;
			}
		}
		
		public function getList($indexes)
		{
			$this->ensureTriedToConnect();
			
			return
				($return = $this->get($indexes))
					? $return
					: array();
		}
		
		public function get($index)
		{
			$this->ensureTriedToConnect();
			
			try {
				return $this->instance->get($index);
			} catch (Exception $e) {
				if(strpos($e->getMessage(), 'Invalid key') !== false)
					return null;
				
				$this->alive = false;
				
				return null;
			}
			
			Assert::isUnreachable();
		}
		
		public function delete($index)
		{
			$this->ensureTriedToConnect();
			
			try {
				// second parameter required, wrt new memcached protocol:
				// delete key 0 (see process_delete_command in the memcached.c)
				// Warning: it is workaround!
				return $this->instance->delete($index, 0);
			} catch (Exception $e) {
				return $this->alive = false;
			}
			
			Assert::isUnreachable();
		}
		
		public function append($key, $data)
		{
			$this->ensureTriedToConnect();
			
			try {
				return $this->instance->append($key, $data);
			} catch (Exception $e) {
				return $this->alive = false;
			}
			
			Assert::isUnreachable();
		}
		
		/**
		 * @param float $requestTimeout time in seconds
		 * @return PeclMemcached
		 */
		public function setTimeout($requestTimeout)
		{
			$this->requestTimeout = $requestTimeout;

			return $this;
		}
		
		/**
		 * @return float 
		 */
		public function getTimeout()
		{
			return $this->requestTimeout;
		}

		/**
		* @return array
		**/
		public function getStats()
		{
			return $this->instance->getStats();
		}

		protected function ensureTriedToConnect()
		{
			if ($this->triedConnect) 
				return $this;
			
			$this->triedConnect = true;
			
			$this->connect();
			
			return $this;
		}
		
		protected function store(
			$action, $key, $value, $expires = Cache::EXPIRES_MEDIUM
		)
		{
			$this->ensureTriedToConnect();
			try {
				return
					$this->instance->$action($key, $value, $expires);
			} catch (BaseException $e) {
				return $this->alive = false;
			}
			
			Assert::isUnreachable();
		}
		
		protected function connect()
		{
			try {
				if ($this->persistentId) {
					$this->instance = new Memcached($this->persistentId);
				} else {
					$this->instance = new Memcached();
				}

				if ($this->compress) {
					$this->instance->setOption(Memcached::OPT_COMPRESSION, true);
				}

				$this->instance->setOption(Memcached::OPT_TCP_NODELAY, true);
				$this->instance->setOption(Memcached::OPT_TCP_KEEPALIVE, true);
//				$this->instance->setOption(Memcached::OPT_NO_BLOCK, true);
				$this->instance->setOption(Memcached::OPT_CONNECT_TIMEOUT, $this->connectTimeout);

				$this->instance->addServer($this->host, $this->port);
				$this->alive = true;
			} catch (Exception $e) {}
		}
	}
?>

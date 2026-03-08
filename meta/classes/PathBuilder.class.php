<?php

/***************************************************************************
 *   Copyright (C) 2026 by SourceCraft Code Assistant Agent                *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 ***************************************************************************/

	/**
	 * @ingroup MetaBase
	**/
	class PathBuilder
	{
		private $configuration = null;

		/**
		 * @return PathBuilder
		**/
		public static function create(MetaPathConfigurationInterface $configuration)
		{
			return new self($configuration);
		}

		public function __construct(MetaPathConfigurationInterface $configuration)
		{
			$this->configuration = $configuration;
		}

		/**
		 * @return string
		**/
		public function getBusinessPath(MetaNamespace $ns = null, $auto = false)
		{
			return $this->getPath('business', $ns, $auto);
		}

		/**
		 * @return string
		**/
		public function getDaoPath(MetaNamespace $ns = null, $auto = false)
		{
			return $this->getPath('dao', $ns, $auto);
		}

		/**
		 * @return string
		**/
		public function getProtoPath(MetaNamespace $ns = null, $auto = false)
		{
			return $this->getPath('proto', $ns, $auto);
		}

		/**
		 * @return string
		**/
		public function getAutoBusinessPath(MetaNamespace $ns = null)
		{
			return $this->getBusinessPath($ns, true);
		}

		/**
		 * @return string
		**/
		public function getAutoDaoPath(MetaNamespace $ns = null)
		{
			return $this->getDaoPath($ns, true);
		}

		/**
		 * @return string
		**/
		public function getAutoProtoPath(MetaNamespace $ns = null)
		{
			return $this->getProtoPath($ns, true);
		}

		/**
		 * @return string
		**/
		public function getSchemaPath()
		{
			return $this->configuration->getAutoDir() . 'schema.php';
		}

		/**
		 * @return string
		**/
		private function getPath($type, MetaNamespace $ns = null, $auto = false)
		{
			if ($ns) {
				return $ns->buildFilePath($type, $auto);
			}

			switch ($type) {
				case 'business':
					return $auto
						? $this->configuration->getAutoBusinessDir()
						: $this->configuration->getBusinessDir();

				case 'dao':
					return $auto
						? $this->configuration->getAutoDaoDir()
						: $this->configuration->getDaoDir();

				case 'proto':
					return $auto
						? $this->configuration->getAutoProtoDir()
						: $this->configuration->getProtoDir();

				default:
					throw new WrongArgumentException("unknown type '{$type}'");
			}
		}
	}
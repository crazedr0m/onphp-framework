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
	class DefaultMetaPathConfiguration
		implements MetaPathConfigurationInterface
	{
		/**
		 * @return DefaultMetaPathConfiguration
		**/
		public static function create()
		{
			return new self;
		}

		/**
		 * @return string
		**/
		public function getBusinessDir()
		{
			return ONPHP_META_BUSINESS_DIR;
		}

		/**
		 * @return string
		**/
		public function getDaoDir()
		{
			return ONPHP_META_DAO_DIR;
		}

		/**
		 * @return string
		**/
		public function getProtoDir()
		{
			return ONPHP_META_PROTO_DIR;
		}

		/**
		 * @return string
		**/
		public function getAutoBusinessDir()
		{
			return ONPHP_META_AUTO_BUSINESS_DIR;
		}

		/**
		 * @return string
		**/
		public function getAutoDaoDir()
		{
			return ONPHP_META_AUTO_DAO_DIR;
		}

		/**
		 * @return string
		**/
		public function getAutoProtoDir()
		{
			return ONPHP_META_AUTO_PROTO_DIR;
		}

		/**
		 * @return string
		**/
		public function getAutoDir()
		{
			return ONPHP_META_AUTO_DIR;
		}
	}
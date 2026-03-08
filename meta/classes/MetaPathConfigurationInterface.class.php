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
	interface MetaPathConfigurationInterface
	{
		/**
		 * @return string
		**/
		public function getBusinessDir();

		/**
		 * @return string
		**/
		public function getDaoDir();

		/**
		 * @return string
		**/
		public function getProtoDir();

		/**
		 * @return string
		**/
		public function getAutoBusinessDir();

		/**
		 * @return string
		**/
		public function getAutoDaoDir();

		/**
		 * @return string
		**/
		public function getAutoProtoDir();

		/**
		 * @return string
		**/
		public function getAutoDir();
	}
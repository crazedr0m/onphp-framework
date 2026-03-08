<?php

/***************************************************************************
 *   Copyright (C) 2006-2007 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * @ingroup Patterns
	**/
	abstract class BasePattern extends Singleton implements GenerationPattern
	{
		public function tableExists()
		{
			return true;
		}

		public function daoExists()
		{
			return false;
		}

		public static function dumpFile($path, $content)
		{
			$content = trim($content);

			if (is_readable($path)) {
				$pattern =
					[
						'@\/\*(.*)\*\/@sU',
						'@[\r\n]@sU'
					];

				// strip only header and svn's Id-keyword, don't skip type hints
				$old = preg_replace($pattern, null, file_get_contents($path), 2);
				$new = preg_replace($pattern, null, $content, 2);
			} else {
				$old = 1;
				$new = 2;
			}

			$out = MetaConfiguration::out();
			$className = basename($path, EXT_CLASS);

			if ($old !== $new) {
				$out->
					warning("\t\t" . $className . ' ');

				if (!MetaConfiguration::me()->isDryRun()) {
					$fp = fopen($path, 'wb');
					fwrite($fp, $content.PHP_EOL.PHP_EOL);
					fclose($fp);
				}

				$out->
					log('(')->
					remark(
						str_replace(getcwd() . DIRECTORY_SEPARATOR, null, $path)
					)->
					logLine(')');
			} else {
				$out->
					infoLine("\t\t" . $className . ' ', true);
			}
		}

		public function build(MetaClass $class)
		{
			return $this->fullBuild($class);
		}

		/**
		 * @return BasePattern
		**/
		protected function fullBuild(MetaClass $class)
		{
			return $this->
				buildProto($class)->
				buildBusiness($class)->
				buildDao($class);
		}

		/**
		 * @return BasePattern
		**/
		protected function buildProto(MetaClass $class)
		{
			$ns = $class->getNameSpace();
			if ($ns) {
				$autoFile = $ns->buildFilePath('proto', true) . $class->getName() . EXT_CLASS;
				$userFile = $ns->buildFilePath('proto') . $class->getName() . EXT_CLASS;
			} else {
				$autoFile = MetaConfiguration::me()->getPathBuilder()->getAutoProtoPath() . 'AutoProto' . $class->getName() . EXT_CLASS;
				$userFile = MetaConfiguration::me()->getPathBuilder()->getProtoPath() . 'Proto' . $class->getName() . EXT_CLASS;
			}

			$this->dumpFile(
				$autoFile,
				Format::indentize(AutoProtoClassBuilder::build($class))
			);

			if (
				MetaConfiguration::me()->isForcedGeneration()
				|| !file_exists($userFile)
			) {
				$this->dumpFile(
					$userFile,
					Format::indentize(ProtoClassBuilder::build($class))
				);
            }

			return $this;
		}

		/**
		 * @return BasePattern
		**/
		protected function buildBusiness(MetaClass $class)
		{
			$ns = $class->getNameSpace();
			if ($ns) {
				$autoFile = $ns->buildFilePath('business', true) . $class->getName() . EXT_CLASS;
				$userFile = $ns->buildFilePath('business') . $class->getName() . EXT_CLASS;
			} else {
				$autoFile = MetaConfiguration::me()->getPathBuilder()->getAutoBusinessPath() . 'Auto' . $class->getName() . EXT_CLASS;
				$userFile = MetaConfiguration::me()->getPathBuilder()->getBusinessPath() . $class->getName() . EXT_CLASS;
			}

			$this->dumpFile(
				$autoFile,
				Format::indentize(AutoClassBuilder::build($class))
			);

			if (
				MetaConfiguration::me()->isForcedGeneration()
				|| !file_exists($userFile)
			) {
				$this->dumpFile(
					$userFile,
					Format::indentize(BusinessClassBuilder::build($class))
				);
            }

			return $this;
		}

		/**
		 * @return BasePattern
		**/
		protected function buildDao(MetaClass $class)
		{
			$ns = $class->getNameSpace();
			if ($ns) {
				$autoFile = $ns->buildFilePath('dao', true) . $class->getName() . 'DAO' . EXT_CLASS;
				$userFile = $ns->buildFilePath('dao') . $class->getName() . 'DAO' . EXT_CLASS;
			} else {
				$autoFile = MetaConfiguration::me()->getPathBuilder()->getAutoDaoPath() . 'Auto' . $class->getName() . 'DAO' . EXT_CLASS;
				$userFile = MetaConfiguration::me()->getPathBuilder()->getDaoPath() . $class->getName() . 'DAO' . EXT_CLASS;
			}

			$this->dumpFile(
				$autoFile,
				Format::indentize(AutoDaoBuilder::build($class))
			);

			if (
				MetaConfiguration::me()->isForcedGeneration()
				|| !file_exists($userFile)
			) {
				$this->dumpFile(
					$userFile,
					Format::indentize(DaoBuilder::build($class))
				);
            }

			return $this;
		}
	}

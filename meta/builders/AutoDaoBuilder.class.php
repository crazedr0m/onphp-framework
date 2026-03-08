<?php

/***************************************************************************
 *   Copyright (C) 2006-2008 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * @ingroup Builders
	**/
	final class AutoDaoBuilder extends BaseBuilder
	{
		public static function build(MetaClass $class)
		{
			if (!$class->hasBuildableParent()) {
				return DictionaryDaoBuilder::build($class);
			} else {
$parent = $class->getParent();
            }

			$ns = $class->getNameSpace();
			$out = self::getHead();

			if ($ns) {
				$out .= <<<EOT
namespace {$ns->buildFullName('dao', true)};

EOT;
			}

			$className = $ns ? $class->getName() . 'DAO' : 'Auto' . $class->getName() . 'DAO';

			if (
				$class->getParent()->getPattern()
					instanceof InternalClassPattern
			) {
				$parentName = ($ns ? '\\' : '') . 'StorableDAO';
			} else {
				if ($parent->getNameSpace()) {
					$parentName = $parent->getNameSpace()->buildFullName('dao', true) . '\\' . $parent->getName() . 'DAO';
				} else {
					$parentName = ($ns ? '\\' : '') . $parent->getName() . 'DAO';
				}
			}

			$out .= <<<EOT
abstract class {$className} extends {$parentName}
{

EOT;

			$out .= self::buildPointers($class) . "\n}\n";

			return $out . self::getHeel();
		}
	}

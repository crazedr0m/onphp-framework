<?php

/***************************************************************************
 *   Copyright (C) 2012 by Georgiy T. Kutsurua                             *
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
	final class EnumClassBuilder extends OnceBuilder
	{
		public static function build(MetaClass $class)
		{
			$ns = $class->getNameSpace();
			$out = self::getHead();

			if ($ns) {
				$out .= <<<EOT
namespace {$ns->buildFullName('business', false)};

EOT;
			}

			if ($type = $class->getType()) {
				$type = "{$type->getName()} ";
			} else {
$type = null;
            }
			$parent = ($ns ? '\\' : '') . 'Enum';
			$out .= <<<EOT
{$type}class {$class->getName()} extends {$parent}
{
	// implement me!
	protected static \$names = array();
}

EOT;

			return $out . self::getHeel();
		}
	}

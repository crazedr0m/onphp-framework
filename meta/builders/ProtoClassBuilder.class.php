<?php

/***************************************************************************
 *   Copyright (C) 2007 by Konstantin V. Arkhipov                          *
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
	final class ProtoClassBuilder extends OnceBuilder
	{
		public static function build(MetaClass $class)
		{
			$ns = $class->getNameSpace();

			$out = self::getHead();

			$className = "Proto{$class->getName()}";
			$parentName = "AutoProto{$class->getName()}";
			if ($ns) {
				$className = $class->getName();
				$parentName = $ns->buildFullName('proto', true) . '\\' . $className;
				$out .= <<<EOT
namespace {$ns->buildFullName('proto', false)};

EOT;
			}

			if ($type = $class->getType()) {
				$typeName = $type->toString() . ' ';
			} else {
$typeName = null;
            }


			$out .= <<<EOT
{$typeName}class {$className} extends {$parentName} {/*_*/}

EOT;

			return $out . self::getHeel();
		}
	}

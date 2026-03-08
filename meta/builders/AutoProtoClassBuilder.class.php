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
	final class AutoProtoClassBuilder extends BaseBuilder
	{
		public static function build(MetaClass $class)
		{
			$ns = $class->getNameSpace();
			$out = self::getHead();

			if ($ns) {
				$out .= <<<EOT
namespace {$ns->buildFullName('proto', true)};

EOT;
			}

			$parent = $class->getParent();
			
			if ($class->hasBuildableParent()) {
				if ($parent->getNameSpace()) {
					$parentName = $parent->getNameSpace()->buildFullName('proto').'\\'.$parent->getName();
				} else {
					$parentName = ($ns ? '\\': '').'Proto'.$parent->getName();
				}
			} else {
				$parentName = ($ns ? '\\': '').'AbstractProtoClass';
			}

			$className = $ns ? $class->getName() : "AutoProto{$class->getName()}";
			$out .= <<<EOT
abstract class {$className} extends {$parentName}
{
EOT;
			$classDump = self::dumpMetaClass($class);
			
			$out .= <<<EOT

{$classDump}
}

EOT;

			return $out.self::getHeel();
		}
		
		private static function dumpMetaClass(MetaClass $class)
		{
			$propertyList = $class->getWithInternalProperties();
			
			$out = <<<EOT
	protected function makePropertyList()
	{

EOT;

			if ($class->hasBuildableParent()) {
				$out .= <<<EOT
		return
			array_merge(
				parent::makePropertyList(),
				array(

EOT;
				if ($class->getIdentifier()) {
					$propertyList[$class->getIdentifier()->getName()] =
						$class->getIdentifier();
				}
			} else {
				$out .= <<<EOT
		return array(

EOT;
			}
			
			$list = [];
			
			foreach ($propertyList as $property) {
				$list[] =
					"'{$property->getName()}' => "
					.$property->toLightProperty($class)->toString();
			}
			
			$out .= implode(",\n", $list);
			
			if ($class->hasBuildableParent()) {
				$out .= "\n)";
			}
			
			$out .= <<<EOT

		);
	}
EOT;
			return $out;
		}
	}
?>
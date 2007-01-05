<?php
/***************************************************************************
 *   Copyright (C) 2006-2007 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	/**
	 * @ingroup Builders
	**/
	final class ContainerClassBuilder extends OnceBuilder
	{
		public static function build(MetaClass $class)
		{
			throw new UnsupportedMethodException();
		}
		
		public static function buildContainer(
			MetaClass $class, MetaClassProperty $holder
		)
		{
			$out = self::getHead();
			
			$containerName = $class->getName().ucfirst($holder->getName()).'DAO';
			
			$out .=
				'final class '
				.$containerName
				.' extends '
				.$holder->getRelation()->toString().'Linked'
				."\n{\n";

			$className = $class->getName();
			$propertyName = strtolower($className[0]).substr($className, 1);
			
			$remoteDumbName = $holder->getClass()->getDumbName();
			
			$out .= <<<EOT
public function __construct({$className} \${$propertyName}, \$lazy = false)
{
	parent::__construct(
		\${$propertyName},
		{$holder->getType()->getClassName()}::dao(),
		\$lazy
	);
}

/**
 * @return {$containerName}
**/
public static function create({$className} \${$propertyName}, \$lazy = false)
{
	return new self(\${$propertyName}, \$lazy);
}

EOT;

			if ($holder->getRelation()->getId() == MetaRelation::MANY_TO_MANY) {
				$out .= <<<EOT

public static function getHelperTable()
{
	return '{$class->getDumbName()}_{$remoteDumbName}';
}

public static function getChildIdField()
{
	return '{$remoteDumbName}_id';
}

EOT;
			}
			
			$out .= <<<EOT

public static function getParentIdField()
{
	return '{$class->getDumbName()}_id';
}

EOT;
			
			
			$out .= "}\n";
			$out .= self::getHeel();
			
			return $out;
		}
	}
?>
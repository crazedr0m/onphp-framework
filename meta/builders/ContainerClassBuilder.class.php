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
	 * @ingroup Builders
	**/
	final class ContainerClassBuilder extends OnceBuilder
	{
		public static function build(MetaClass $class)
		{
			throw new UnsupportedMethodException();
		}

		public static function buildContainer(
			MetaClass $class,
            MetaClassProperty $holder
		) {
			$ns = $class->getNameSpace();
			$out = self::getHead();

			if ($ns) {
				$out .= <<<EOT
namespace {$ns->buildFullName('dao', false)};

EOT;
			}

			$containerName = $class->getName() . ucfirst($holder->getName()) . 'DAO';

			$out .=
				'final class '
				. $containerName
				. ' extends ' . ($ns ? '\\' : '')
				. $holder->getRelation()->toString() . 'Linked'
				. "\n{\n";

			$className = $class->getName();
			$propertyName = strtolower($className[0]) . substr($className, 1);

			$remoteColumnName = $holder->getType()->getClass()->getTableName();

			// Полное имя бизнес-класса
			$businessNs = $class->getNameSpace();
			$fullClassName = $businessNs
				? $businessNs->buildFullName('business', false) . '\\' . $className
				: ($ns ? '\\' : '') . $className;

			// Полное имя класса свойства
			$propertyClass = $holder->getType()->getClass();
			$propertyNs = $propertyClass->getNameSpace();
			$fullPropertyClassName = $propertyNs
				? $propertyNs->buildFullName('business', false) . '\\' . $propertyClass->getName()
				: ($ns ? '\\' : '') . $propertyClass->getName();

			$out .= <<<EOT
public function __construct({$fullClassName} \${$propertyName}, \$lazy = false)
{
	parent::__construct(
		\${$propertyName},
		{$fullPropertyClassName}::dao(),
		\$lazy
	);
}

/**
	* @return {$containerName}
**/
public static function create({$fullClassName} \${$propertyName}, \$lazy = false)
{
	return new self(\${$propertyName}, \$lazy);
}

EOT;

			if ($holder->getRelation()->getId() == MetaRelation::MANY_TO_MANY) {
				$out .= <<<EOT

public function getHelperTable()
{
	return '{$class->getTableName()}_{$remoteColumnName}';
}

public function getChildIdField()
{
	return '{$remoteColumnName}_id';
}

EOT;
			}

			$out .= <<<EOT

public function getParentIdField()
{
	return '{$class->getTableName()}_id';
}

EOT;


			$out .= "}\n";
			$out .= self::getHeel();

			return $out;
		}
	}

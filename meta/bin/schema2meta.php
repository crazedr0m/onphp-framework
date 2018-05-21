#!/usr/bin/php
<?php
/***************************************************************************
 *   Copyright (C) 2017 by Igor V. Gulyaev                                 *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

/**
 * Generate meta xml files from MySQL schema dump
 *
 * Usage: schema2meta.php schema.sql > meta.xml
 *
**/

echo '<?xml version="1.0"?>'
?>

<!DOCTYPE metaconfiguration SYSTEM "meta.dtd">

<metaconfiguration>
<classes>

<?php
	$startExpression = 'CREATE TABLE `(?<tableName>.*?)` \(';
	$stopExpression = "\)( ENGINE=.*?)( COMMENT='(?<comment>.*?)'){0,1};";
	$primaryExpression = 'PRIMARY KEY  \(`(?<fieldName>.*?)`\)';
	$fieldExpression = "`(?<fieldName>.+?)` (?<fieldType>\w+)(\((?<fieldSize>.+?)\)){0,1}( unsigned){0,1}((?<notNull> NOT){0,1} NULL){0,1}( default (?<default>'(.*?)'|NULL)){0,1}( auto_increment){0,1}( comment '(?<comment>.*?)'){0,1},{0,1}";

	$schemaFile = file($argv[1]);
	$table = array();
	$tableStarted = false;
	while ($line = next($schemaFile)) {

		$line = trim($line);

		$matches = array();
		if (
			preg_match('~'.$stopExpression.'~', $line, $matches)
			&& $tableStarted
		) {
			if (array_key_exists('comment', $matches)) {
				$table['comment'] = $matches['comment'];
			}
			$tableStarted = false;
			table2meta($table);
			$table = array();
		}

		$matches = array();
		if (
			preg_match('~'.$primaryExpression.'~', $line, $matches)
			&& $tableStarted
		) {
			$table['pk'] = $matches['fieldName'];
		}

		$matches = array();
		if (
			preg_match('~'.$fieldExpression.'~i', $line, $matches)
			&& $tableStarted
		) {
			$table['fields'][$matches['fieldName']] = $matches;
		}

		$matches = array();
		if (
			preg_match('~'.$startExpression.'~', $line, $matches)
			&& !$tableStarted
		) {
			$table['name'] = $matches['tableName'];
			$table['fields'] = array();
			$table['pk'] = null;

			$tableStarted = true;
		}
	}
?>
</classes>
</metaconfiguration>
<?php
	function table2meta($table)
	{
		$typeMap = array(
			'varchar'	=> 'String',
			'tinytext'	=> 'String',
			'text'		=> 'String',
			'char'		=> 'FixedLengthString',
			'enum'		=> 'Enum',
			'set'		=> 'Enum',

			'tinyint'	=> 'SmallInteger',
			'smallint'	=> 'SmallInteger',
			'bigint'	=> 'BigInteger',
			'mediumint'	=> 'Integer',
			'int'		=> 'Integer',
			'year'		=> 'Integer',

			'datetime'	=> 'Timestamp',
			'timestamp'	=> 'Timestamp',
			'date'		=> 'Date',

			'float'		=> 'Float',
			'double'	=> 'Double',
			'decimal'	=> 'Numeric'
		);

		$tableName = $table['name'];
		$classNameArray = explode('_', $tableName);
		$className = '';
		foreach ($classNameArray as $word) {
			$className .= ucfirst($word);
		}
		if (array_key_exists('comment', $table)) {
?>
	<!-- <?=$table['comment']?> -->
<?php
		}
?>
	<class type="final" name="<?=$className?>" table="<?=$tableName?>">
		<properties>
<?php
		foreach ($table['fields'] as $fieldName => $props) {

			$attributesList = array();
			
			$column = $fieldName;

			$attributesList['name'] =
				preg_replace_callback(
					'/_([a-z0-9])/',
					function($matches) {
						return strtolower($matches[0]);
					},
					$fieldName
				);

			$isPrimaryKey = ($table['pk'] == $column);

			$tag = 'property';
			if ($isPrimaryKey)
				$tag = 'identifier';

			$type = $props['fieldType'];

			if (isset($typeMap[$type]))
				$type = $typeMap[$type];

			$attributesList['type'] = $type;

			$hasSize = !in_array(
				$type,
				array('Timestamp', 'Date', 'SmallInteger', 'Integer', 'BigInteger')
			);

			if (
				$hasSize
				&& isset($props['fieldSize'])
				&& $props['fieldSize']
			) {
				$attributesList['size'] = $props['fieldSize'];
			}

			$isDateTime = in_array($type, array('Timestamp', 'Date'));

			if (!$isPrimaryKey) {
				$default = null;
				if (isset($props['default']) && !$isDateTime) {
					$default = str_replace("'", '', $props['default']);
					if ($default && $default != 'NULL')
						$attributesList['default'] = $default;
				}

				$attributesList['required'] = (isset($props['notNull']) ? 'true' : 'false');

				if ($default == 'NULL')
					$attributesList['required'] = 'false';
			}

			$attributesList['column'] = $column;
			
			$attributesString = '';
			foreach ($attributesList as $key => $value) {
				$attributesString .= " $key".'="'.$value.'"';
			}
			if (array_key_exists('comment', $props)) {
?>
			<!-- <?=$props['comment']?> -->
<?php
			}
?>
			<<?=$tag?><?=$attributesString?> />
<?php
		}
?>
		</properties>

		<pattern name="StraightMapping" />
	</class>

<?php
	}
?>
<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2012-10-21 00:51:03                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	namespace Onphp\NsConverter;

	$schema = new \Onphp\DBSchema();
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_template')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(64),
					'name'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BOOLEAN)->
					setNull(false),
					'is_current'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_iterator_path')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(64),
					'path'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'template_id'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_file')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(256),
					'full_path'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'path_id'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_iterator_rule')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::TEXT)->
					setNull(false),
					'pattern'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BOOLEAN)->
					setNull(false),
					'is_white'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_class')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(256),
					'name'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setSize(256),
					'namespace'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setSize(256),
					'namespace_new'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'template_id'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_function')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(256),
					'name'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'template_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT),
					'class_id'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_class_link')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'used_class_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT),
					'user_class_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT),
					'user_function_id'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_class_file')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_admin')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(64),
					'name'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(128),
					'email'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setNull(false)->
					setSize(32),
					'password'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_log')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'id'
				)->
				setPrimaryKey(true)->
				setAutoincrement(true)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::TEXT)->
					setNull(false),
					'message'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setSize(64),
					'object_name'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::VARCHAR)->
					setSize(64),
					'object_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::INTEGER)->
					setNull(false),
					'admin_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::TIMESTAMP)->
					setNull(false),
					'insert_date'
				)
			)
		);
	
	$schema->
		addTable(
			\Onphp\DBTable::create('con_template_con_iterator_rule')->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'con_iterator_rule_id'
				)
			)->
			addColumn(
				\Onphp\DBColumn::create(
					\Onphp\DataType::create(\Onphp\DataType::BIGINT)->
					setNull(false),
					'con_template_id'
				)
			)->
			addUniques('con_iterator_rule_id', 'con_template_id')
		);
	
	// con_iterator_path.template_id -> con_template.id
	$schema->
		getTableByName('con_iterator_path')->
		getColumnByName('template_id')->
		setReference(
			$schema->
				getTableByName('con_template')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_file.path_id -> con_iterator_path.id
	$schema->
		getTableByName('con_file')->
		getColumnByName('path_id')->
		setReference(
			$schema->
				getTableByName('con_iterator_path')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_class.template_id -> con_template.id
	$schema->
		getTableByName('con_class')->
		getColumnByName('template_id')->
		setReference(
			$schema->
				getTableByName('con_template')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_function.template_id -> con_template.id
	$schema->
		getTableByName('con_function')->
		getColumnByName('template_id')->
		setReference(
			$schema->
				getTableByName('con_template')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_function.class_id -> con_class.id
	$schema->
		getTableByName('con_function')->
		getColumnByName('class_id')->
		setReference(
			$schema->
				getTableByName('con_class')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_class_link.used_class_id -> con_class.id
	$schema->
		getTableByName('con_class_link')->
		getColumnByName('used_class_id')->
		setReference(
			$schema->
				getTableByName('con_class')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_class_link.user_class_id -> con_class.id
	$schema->
		getTableByName('con_class_link')->
		getColumnByName('user_class_id')->
		setReference(
			$schema->
				getTableByName('con_class')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
	// con_class_link.user_function_id -> con_function.id
	$schema->
		getTableByName('con_class_link')->
		getColumnByName('user_function_id')->
		setReference(
			$schema->
				getTableByName('con_function')->
				getColumnByName('id'),
				\Onphp\ForeignChangeAction::restrict(),
				\Onphp\ForeignChangeAction::cascade()
			);
	
?>
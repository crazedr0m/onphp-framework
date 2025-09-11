<?php
/***************************************************************************
 *   Copyright (C) 2004-2009 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	// sample system-wide configuration file
	
	function error2Exception($code, $string, $file, $line, $context)
	{
		throw new BaseException($string, $code);
	}
	
	// file extensions
	define('EXT_CLASS', '.class.php');
	define('ONPHP_V1_EXT_CLASS', '.class.php');
	define('EXT_TPL', '.tpl.html');
	define('EXT_MOD', '.inc.php');
	define('EXT_HTML', '.html');
	define('EXT_UNIT', '.unit.php');
	
	// overridable constant, don't forget for trailing slash
	// also you may consider using /dev/shm/ for cache purposes
	if (!defined('ONPHP_TEMP_PATH')) {
		$tempSuffix = 'onPHP';
		if (isset($_SERVER['USER'])) {
			$tempSuffix .= '-'.$_SERVER['USER'];
		}
		define(
			'ONPHP_TEMP_PATH',
			sys_get_temp_dir().DIRECTORY_SEPARATOR.$tempSuffix.DIRECTORY_SEPARATOR
		);
	}
	
	// system settings
	error_reporting(E_ALL | E_STRICT);
	set_error_handler('error2Exception', E_ALL | E_STRICT);
	ignore_user_abort(true);
	define('ONPHP_VERSION', '1.1.master');
	
	if (!defined('ONPHP_IPC_PERMS'))
		define('ONPHP_IPC_PERMS', 0660);
	
	// paths
	define('ONPHP_ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
	define('ONPHP_V1_ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
	define('ONPHP_V1_CORE_PATH', ONPHP_V1_ROOT_PATH.'core'.DIRECTORY_SEPARATOR);
	define('ONPHP_V1_MAIN_PATH', ONPHP_V1_ROOT_PATH.'main'.DIRECTORY_SEPARATOR);
	define('ONPHP_V1_META_PATH', ONPHP_V1_ROOT_PATH.'meta'.DIRECTORY_SEPARATOR);
	define('ONPHP_V1_UI_PATH', ONPHP_ROOT_PATH.'UI'.DIRECTORY_SEPARATOR);
	define('ONPHP_CORE_PATH', ONPHP_ROOT_PATH.'core'.DIRECTORY_SEPARATOR);
	define('ONPHP_MAIN_PATH', ONPHP_ROOT_PATH.'main'.DIRECTORY_SEPARATOR);
	define('ONPHP_META_PATH', ONPHP_ROOT_PATH.'meta'.DIRECTORY_SEPARATOR);
	define('ONPHP_UI_PATH', ONPHP_ROOT_PATH.'UI'.DIRECTORY_SEPARATOR);

	/**
	 * @deprecated 
	 */
	if (!defined('ONPHP_CURL_CLIENT_OLD_TO_STRING'))
		define('ONPHP_CURL_CLIENT_OLD_TO_STRING', false);
	
	define('ONPHP_V1_META_CLASSES', ONPHP_V1_META_PATH.'classes'.DIRECTORY_SEPARATOR);
	define('ONPHP_META_CLASSES', ONPHP_V1_META_PATH.'classes'.DIRECTORY_SEPARATOR);

	define(
		'ONPHP_V1_INCUBATOR_PATH',
		ONPHP_V1_ROOT_PATH.'incubator'.DIRECTORY_SEPARATOR
	);
	
	if (!defined('ONPHP_V1_CLASS_CACHE'))
		define('ONPHP_V1_CLASS_CACHE', ONPHP_TEMP_PATH);

	if (!defined('ONPHP_CLASS_CACHE'))
		define('ONPHP_CLASS_CACHE', ONPHP_TEMP_PATH);

	// classes autoload magic
	if (!defined('ONPHP_V1_CLASS_CACHE_TYPE'))
		define('ONPHP_V1_CLASS_CACHE_TYPE', 'AutoloaderClassPathCache');

	if (!defined('ONPHP_CLASS_CACHE_TYPE'))
		define('ONPHP_CLASS_CACHE_TYPE', 'AutoloaderClassPathCache');

	require ONPHP_V1_MAIN_PATH.'Autoloader'.DIRECTORY_SEPARATOR.'require'.EXT_MOD;
	
	$autoloader = ONPHP_V1_CLASS_CACHE_TYPE;
	AutoloaderPool::set('onPHP', $autoloader = new $autoloader());
	/* @var $autoloader AutoloaderClassPathCache */
	$autoloader->setNamespaceResolver(NamespaceResolverOnPHP::create())->register();
	
	$autoloader->addPaths(array(
		// core classes
		ONPHP_V1_CORE_PATH.'Base',
		ONPHP_V1_CORE_PATH.'Cache',
		
		ONPHP_V1_CORE_PATH.'DB',
		ONPHP_V1_CORE_PATH.'DB'.DIRECTORY_SEPARATOR.'Transaction',
		ONPHP_V1_CORE_PATH.'DB'.DIRECTORY_SEPARATOR.'NoSQL',
		
		ONPHP_V1_CORE_PATH.'Exceptions',
		
		ONPHP_V1_CORE_PATH.'Form',
		ONPHP_V1_CORE_PATH.'Form'.DIRECTORY_SEPARATOR.'Filters',
		ONPHP_V1_CORE_PATH.'Form'.DIRECTORY_SEPARATOR.'Primitives',
		
		ONPHP_V1_CORE_PATH.'Logic',
		ONPHP_V1_CORE_PATH.'OSQL',
		
		// main framework
		ONPHP_V1_MAIN_PATH.'Base',
		
		ONPHP_V1_MAIN_PATH.'Criteria',
		ONPHP_V1_MAIN_PATH.'Criteria'.DIRECTORY_SEPARATOR.'Projections',
		
		ONPHP_V1_MAIN_PATH.'Crypto',
		
		ONPHP_V1_MAIN_PATH.'DAOs',
		ONPHP_V1_MAIN_PATH.'DAOs'.DIRECTORY_SEPARATOR.'Handlers',
		ONPHP_V1_MAIN_PATH.'DAOs'.DIRECTORY_SEPARATOR.'Workers',
		ONPHP_V1_MAIN_PATH.'DAOs'.DIRECTORY_SEPARATOR.'Uncachers',
		
		ONPHP_V1_MAIN_PATH.'Flow',
		ONPHP_V1_MAIN_PATH.'SPL',
		
		ONPHP_V1_MAIN_PATH.'Net',
		ONPHP_V1_MAIN_PATH.'Net'.DIRECTORY_SEPARATOR.'Http',
		ONPHP_V1_MAIN_PATH.'Net'.DIRECTORY_SEPARATOR.'Mail',
		ONPHP_V1_MAIN_PATH.'Net'.DIRECTORY_SEPARATOR.'Ip',
		ONPHP_V1_MAIN_PATH.'Net'.DIRECTORY_SEPARATOR.'Soap',
		
		ONPHP_V1_MAIN_PATH.'Math',
		
		ONPHP_V1_MAIN_PATH.'Markup',
		ONPHP_V1_MAIN_PATH.'Markup'.DIRECTORY_SEPARATOR.'Feed',
		ONPHP_V1_MAIN_PATH.'Markup'.DIRECTORY_SEPARATOR.'Html',
		
		ONPHP_V1_MAIN_PATH.'OQL',
		ONPHP_V1_MAIN_PATH.'OQL'.DIRECTORY_SEPARATOR.'Expressions',
		ONPHP_V1_MAIN_PATH.'OQL'.DIRECTORY_SEPARATOR.'Parsers',
		ONPHP_V1_MAIN_PATH.'OQL'.DIRECTORY_SEPARATOR.'Statements',
		
		ONPHP_V1_MAIN_PATH.'OpenId',
		
		ONPHP_V1_MAIN_PATH.'EntityProto',
		ONPHP_V1_MAIN_PATH.'EntityProto'.DIRECTORY_SEPARATOR.'Builders',
		ONPHP_V1_MAIN_PATH.'EntityProto'.DIRECTORY_SEPARATOR.'Accessors',
		
		ONPHP_V1_MAIN_PATH.'UnifiedContainer',

		ONPHP_V1_MAIN_PATH.'UI',
		ONPHP_V1_MAIN_PATH.'UI'.DIRECTORY_SEPARATOR.'View',
		
		ONPHP_V1_MAIN_PATH.'Utils',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'TuringTest',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'Archivers',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'IO',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'Logging',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'Mobile',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'CommandLine',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'Routers',

		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'AMQP',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'AMQP'
		.DIRECTORY_SEPARATOR.'Pecl',
		ONPHP_V1_MAIN_PATH.'Utils'.DIRECTORY_SEPARATOR.'AMQP'
		.DIRECTORY_SEPARATOR.'Exceptions',

		ONPHP_V1_MAIN_PATH.'Messages',
		ONPHP_V1_MAIN_PATH.'Messages'.DIRECTORY_SEPARATOR.'Interface',

		ONPHP_V1_MAIN_PATH.'Application',
		
		ONPHP_V1_MAIN_PATH.'Charts',
		ONPHP_V1_MAIN_PATH.'Charts'.DIRECTORY_SEPARATOR.'Google',
		ONPHP_V1_MAIN_PATH.'Monitoring',
		
		ONPHP_V1_META_CLASSES,
		
	/*
		ONPHP_V1_INCUBATOR_PATH
			.'classes'.DIRECTORY_SEPARATOR
			.'Application'.DIRECTORY_SEPARATOR,
			
		ONPHP_V1_INCUBATOR_PATH
			.'classes'.DIRECTORY_SEPARATOR
			.'Application'.DIRECTORY_SEPARATOR
			.'Markups'.DIRECTORY_SEPARATOR,
		
		ONPHP_V1_INCUBATOR_PATH
			.'classes'.DIRECTORY_SEPARATOR
			.'Application'.DIRECTORY_SEPARATOR
			.'Markups'.DIRECTORY_SEPARATOR
			.'Documents'.DIRECTORY_SEPARATOR,
	*/
	));
	
	//NOTE: disable by default
	//see http://pgfoundry.org/docman/view.php/1000079/117/README.txt
	//define('POSTGRES_IP4_ENABLED', true);
?>

<?php

	require_once dirname(__FILE__) . '/../../global.inc.php.tpl';

	$metaDir = dirname(__FILE__) . '/';
	if (!defined('ONPHP_TEST_PATH')) {
		define('ONPHP_TEST_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
	}
	if (!defined('PATH_BASE')) {
		define('PATH_BASE', ONPHP_TEST_PATH . 'meta' . DIRECTORY_SEPARATOR);
	}
	if (!defined('PATH_CLASSES')) {
		define('PATH_CLASSES', PATH_BASE);
	}
	if (!defined('ONPHP_META_PATH')) {
		define('ONPHP_META_PATH', $metaDir);
	}
	if (!defined('ONPHP_META_BUILDERS')) {
		define('ONPHP_META_BUILDERS', ONPHP_META_PATH . 'builders' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_PATTERNS')) {
		define('ONPHP_META_PATTERNS', ONPHP_META_PATH . 'patterns' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_TYPES')) {
		define('ONPHP_META_TYPES', ONPHP_META_PATH . 'types' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_AUTO_DIR')) {
		define('ONPHP_META_AUTO_DIR', PATH_CLASSES . 'Auto' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_AUTO_BUSINESS_DIR')) {
		define('ONPHP_META_AUTO_BUSINESS_DIR', ONPHP_META_AUTO_DIR . 'Business' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_AUTO_DAO_DIR')) {
		define('ONPHP_META_AUTO_DAO_DIR', ONPHP_META_AUTO_DIR . 'DAOs' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_AUTO_PROTO_DIR')) {
		define('ONPHP_META_AUTO_PROTO_DIR', ONPHP_META_AUTO_DIR . 'Proto' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_BUSINESS_DIR')) {
		define('ONPHP_META_BUSINESS_DIR', PATH_CLASSES . 'Business' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_DAO_DIR')) {
		define('ONPHP_META_DAO_DIR', PATH_CLASSES . 'DAOs' . DIRECTORY_SEPARATOR);
	}
	if (!defined('ONPHP_META_PROTO_DIR')) {
		define('ONPHP_META_PROTO_DIR', PATH_CLASSES . 'Proto' . DIRECTORY_SEPARATOR);
	}

	// ensure directories exist
	foreach (
        [
		ONPHP_META_AUTO_BUSINESS_DIR,
		ONPHP_META_AUTO_DAO_DIR,
		ONPHP_META_AUTO_PROTO_DIR,
		ONPHP_META_BUSINESS_DIR,
		ONPHP_META_DAO_DIR,
		ONPHP_META_PROTO_DIR,
        ] as $dir
    ) {
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
	}

	// add meta paths to autoloader
	AutoloaderPool::get('onPHP')->addPaths([
		ONPHP_META_BUILDERS,
		ONPHP_META_PATTERNS,
		ONPHP_META_TYPES,
	]);

	// include meta output classes
	include_once ONPHP_META_PATH . 'classes/ConsoleMode.class.php';
	include_once ONPHP_META_PATH . 'classes/MetaOutput.class.php';
	include_once ONPHP_META_PATH . 'classes/TextOutput.class.php';
	include_once ONPHP_META_PATH . 'classes/ColoredTextOutput.class.php';

	function createMetaConfig()
    {
		$out = new TextOutput();
		$out = new MetaOutput($out);
		$config = MetaConfiguration::me();
		$config->setOutput($out);
		$config->setDryRun(true); // don't write files
		$config->setForcedGeneration(true);
		return $config;
	}

	// test with namespace
	try {
		$config = createMetaConfig();
		// load internal classes first
		$config->load(ONPHP_META_PATH . 'internal.xml', false);
		$config->load($metaDir . 'config.namespace.xml');
		echo "Loading with namespace OK\n";
		$config->buildClasses();
		echo "Building classes OK\n";
		$config->buildContainers();
		echo "Building containers OK\n";
		$config->buildSchema();
		echo "Building schema OK\n";
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage() . "\n";
		echo $e->getTraceAsString() . "\n";
		exit(1);
	}

	// drop singleton to start fresh
	Singleton::dropInstance('MetaConfiguration');

	// test without namespace
	try {
		$config2 = createMetaConfig();
		$config2->load(ONPHP_META_PATH . 'internal.xml', false);
		$config2->load($metaDir . 'config.meta.xml');
		echo "Loading without namespace OK\n";
		$config2->buildClasses();
		echo "Building classes without namespace OK\n";
		$config2->buildContainers();
		echo "Building containers without namespace OK\n";
		$config2->buildSchema();
		echo "Building schema without namespace OK\n";
	} catch (Exception $e) {
		echo "Error without namespace: " . $e->getMessage() . "\n";
		echo $e->getTraceAsString() . "\n";
		exit(1);
	}

	echo "All tests passed.\n";

<?php
	/* $Id$ */

	if (!extension_loaded('onphp')) {
		echo 'Trying to load onPHP extension.. ';
		
		if (!@dl('onphp.so')) {
			echo "failed.\n";
		} else {
			echo "done.\n";
		}
	}
	
	$config = dirname(__FILE__).'/config.inc.php';
	
	include is_readable($config) ? $config : $config.'.tpl';
	
	// provide fake spooked class
	class Spook extends IdentifiableObject {/*_*/}
	
	final class TestSuite extends PHPUnit_Framework_TestSuite
	{
		public function setUp()
		{
			if (AllTests::$workers) {
				$worker = array_pop(AllTests::$workers);
				echo "\nProcessing with {$worker}\n";
				Cache::setDefaultWorker($worker);
			} else {
				$this->markTestSuiteSkipped('No more workers available.');
			}
		}
		
		public function tearDown()
		{
			Cache::dropWorkers();
			echo "\n";
		}
	}
	
	final class AllTests
	{
		public static $dbs = null;
		public static $paths = null;
		public static $workers = null;
		
		public static function main()
		{
			PHPUnit_TextUI_TestRunner::run(self::suite());
		}
		
		public static function suite()
		{
			$suite = new TestSuite('onPHP-'.ONPHP_VERSION);
			
			foreach (self::$paths as $testPath)
				foreach (glob($testPath.'*Test'.EXT_CLASS, GLOB_BRACE) as $file)
					$suite->addTestFile($file);
			
			// meta, DB and DAOs ordered tests portion
			if (self::$dbs) {
				Singleton::getInstance('DBTestPool', self::$dbs)->connect();
				
				// build stuff from meta
				
				$metaDir = ONPHP_TEST_PATH.'meta'.DIRECTORY_SEPARATOR;
				$path = ONPHP_META_PATH.'bin'.DIRECTORY_SEPARATOR.'build.php';
				
				$_SERVER['argv'] = array();
				
				$_SERVER['argv'][0] = $path;
				
				$_SERVER['argv'][1] = $metaDir.'config.inc.php';
				
				$_SERVER['argv'][2] = $metaDir.'config.meta.xml';
				
				$_SERVER['argv'][] = '--force';
				$_SERVER['argv'][] = '--no-schema-check';
				$_SERVER['argv'][] = '--drop-stale-files';
				
				include $path;
				
				// provide paths to autogenerated stuff
				set_include_path(
					get_include_path().PATH_SEPARATOR
					.ONPHP_META_AUTO_BUSINESS_DIR.PATH_SEPARATOR
					.ONPHP_META_AUTO_DAO_DIR.PATH_SEPARATOR
					.ONPHP_META_AUTO_PROTO_DIR.PATH_SEPARATOR
					
					.ONPHP_META_DAO_DIR.PATH_SEPARATOR
					.ONPHP_META_BUSINESS_DIR.PATH_SEPARATOR
					.ONPHP_META_PROTO_DIR
				);
				
				$daoTest = new DAOTest();
				
				$out = MetaConfiguration::me()->getOutput();
				
				foreach (DBTestPool::me()->getPool() as $connector => $db) {
					DBPool::me()->setDefault($db);
					
					$out->
						info('Using ')->
						info(get_class($db), true)->
						infoLine(' connector.');
					
					try {
						$daoTest->drop();
					} catch (DatabaseException $e) {
						// previous shutdown was clean
					}
					
					$daoTest->create()->fill(false);
					
					MetaConfiguration::me()->checkIntegrity();
					$out->newLine();
					
					$daoTest->drop();
				}
				
				DBPool::me()->dropDefault();
			}
			
			$suite->addTestSuite('DAOTest');
			
			return $suite;
		}
	}
	
	AllTests::$dbs = $dbs;
	AllTests::$paths = $testPathes;
	AllTests::$workers = $daoWorkers;
?>
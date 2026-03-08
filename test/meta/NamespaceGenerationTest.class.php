<?php

	require_once dirname(__FILE__) . '/../misc/TestCase.class.php';

	final class NamespaceGenerationTest extends TestCase
	{
		protected $backupGlobals = false;

		private $tempDir;

		protected function setUp()
		{
			parent::setUp();
			// create temporary directory
			$this->tempDir = sys_get_temp_dir() . '/onphp_ns_test_' . mt_rand();
			mkdir($this->tempDir, 0777, true);
		}

		protected function tearDown()
		{
			// remove temporary directory
			if (is_dir($this->tempDir)) {
				system('rm -rf ' . escapeshellarg($this->tempDir));
			}
			parent::tearDown();
		}

		/**
		 * Test generation with namespace.
		 */
		public function testGenerationWithNamespace()
		{
			// load internal classes first
			$config = MetaConfiguration::me();
			$config->setDryRun(false);
			$config->setForcedGeneration(true);

			// modify config.namespace.xml to use our temp dir
			$xmlContent = file_get_contents(dirname(__FILE__) . '/config.namespace.xml');
			$xmlContent = str_replace(
				'base-dir="/tmp/test"',
				'base-dir="' . $this->tempDir . '"',
				$xmlContent
			);
			$tempXml = $this->tempDir . '/config.namespace.xml';
			file_put_contents($tempXml, $xmlContent);

			// load internal.xml (required)
			$config->load(ONPHP_META_PATH . 'internal.xml', false);
			// load our modified config
			$config->load($tempXml);

			// build classes, containers, schema
			$config->buildClasses();
			$config->buildContainers();
			$config->buildSchema();

			// verify that files are created in correct locations
			$expectedPaths = [
				// business classes
				$this->tempDir . '/Vendor/Test/Project/Business/Credentials.class.php',
				$this->tempDir . '/Vendor/Test/Project/Auto/Business/Credentials.class.php',
				$this->tempDir . '/Vendor/Test/Project/Business/TestTree.class.php',
				$this->tempDir . '/Vendor/Test/Project/Auto/Business/TestTree.class.php',
				// proto classes
				$this->tempDir . '/Vendor/Test/Project/Proto/Credentials.class.php',
				$this->tempDir . '/Vendor/Test/Project/Auto/Proto/Credentials.class.php',
				// DAO classes
				$this->tempDir . '/Vendor/Test/Project/DAOs/TestTreeDAO.class.php',
				$this->tempDir . '/Vendor/Test/Project/Auto/DAOs/TestTreeDAO.class.php',
				// container DAOs
				$this->tempDir . '/Vendor/Test/Project/DAOs/TestEncapsulantCitiesDAO.class.php',
			];

			foreach ($expectedPaths as $path) {
				$this->assertTrue(file_exists($path), "File $path should exist");
				// check namespace in file content
				$content = file_get_contents($path);
				// business/dao/proto files should contain namespace declaration
				if (strpos($path, '/Business/') !== false || strpos($path, '/Proto/') !== false || strpos($path, '/DAOs/') !== false) {
					// auto classes have namespace with Auto segment
					if (strpos($path, '/Auto/') !== false) {
						$this->assertContains('namespace Vendor\\Test\\Project\\Auto\\', $content, "Auto class $path should have correct namespace");
					} else {
						$this->assertContains('namespace Vendor\\Test\\Project\\', $content, "Class $path should have correct namespace");
					}
				}
			}

			// verify that no extra "Auto" prefix in class name (except in path)
			$content = file_get_contents($this->tempDir . '/Vendor/Test/Project/Auto/Business/Credentials.class.php');
			$this->assertNotContains('class AutoCredentials', $content, 'Auto business class should not have Auto prefix in class name');
			$this->assertContains('class Credentials', $content, 'Auto business class should be named Credentials');

			// verify that non-auto business class also correct
			$content = file_get_contents($this->tempDir . '/Vendor/Test/Project/Business/Credentials.class.php');
			$this->assertContains('class Credentials', $content, 'Business class should be named Credentials');
		}

		/**
		 * Test generation without namespace (backward compatibility).
		 */
		public function testGenerationWithoutNamespace()
		{
			// we need to reset singleton because previous test may have set namespace
			Singleton::dropInstance('MetaConfiguration');
			$config = MetaConfiguration::me();
			$config->setDryRun(false);
			$config->setForcedGeneration(true);

			// use config.meta.xml (no namespace)
			$config->load(ONPHP_META_PATH . 'internal.xml', false);
			$config->load(dirname(__FILE__) . '/config.meta.xml');

			// build classes
			$config->buildClasses();
			$config->buildContainers();
			$config->buildSchema();

			// verify that files are created in old locations (test/meta/...)
			$expectedPaths = [
				ONPHP_META_AUTO_BUSINESS_DIR . 'AutoCredentials.class.php',
				ONPHP_META_BUSINESS_DIR . 'Credentials.class.php',
				ONPHP_META_AUTO_PROTO_DIR . 'AutoProtoCredentials.class.php',
				ONPHP_META_PROTO_DIR . 'ProtoCredentials.class.php',
				ONPHP_META_AUTO_DAO_DIR . 'AutoTestTreeDAO.class.php',
				ONPHP_META_DAO_DIR . 'TestTreeDAO.class.php',
			];

			foreach ($expectedPaths as $path) {
				$this->assertTrue(file_exists($path), "File $path should exist");
				// check that no namespace declaration
				$content = file_get_contents($path);
				$this->assertNotContains('namespace', $content, "File $path should not contain namespace (backward compatibility)");
			}

			// verify that auto classes have Auto prefix in class name
			$content = file_get_contents(ONPHP_META_AUTO_BUSINESS_DIR . 'AutoCredentials.class.php');
			$this->assertContains('class AutoCredentials', $content, 'Auto business class should have Auto prefix');
		}
	}

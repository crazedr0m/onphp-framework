<?php
	/* $Id$ */

	final class MetaNamespaceTest extends TestCase
	{
		protected $backupGlobals = false;

		/**
		 * @dataProvider buildFullNameProvider
		 */
		public function testBuildFullName($name, $baseSpace, $type, $auto, $expected)
		{
			$ns = new MetaNamespace();
			if ($name !== null) {
				$ns->setName($name);
			}
			if ($baseSpace !== null) {
				$ns->setBaseSpace($baseSpace);
			}
			$result = $ns->buildFullName($type, $auto);
			$this->assertEquals($expected, $result);
		}

		public function buildFullNameProvider()
		{
			return [
				// TODO: заполнить пользователем
				// [name, baseSpace, type, auto, expected]
				['My\\Project', null, 'business', false, 'My\\Project\\Business'],
				['My\\Project', null, 'proto', true, 'My\\Project\\Auto\\Proto'],
				['My\\Project', 'Vendor', 'dao', false, 'Vendor\\My\\Project\\DAOs'],
			];
		}

		/**
		 * @dataProvider buildFilePathProvider
		 */
		public function testBuildFilePath($name, $baseSpace, $baseDir, $type, $auto, $expected)
		{
			$ns = new MetaNamespace();
			if ($name !== null) {
				$ns->setName($name);
			}
			if ($baseSpace !== null) {
				$ns->setBaseSpace($baseSpace);
			}
			if ($baseDir !== null) {
				$ns->setBaseDir($baseDir);
			}
			$result = $ns->buildFilePath($type, $auto);
			$this->assertEquals($expected, $result);
		}

		public function buildFilePathProvider()
		{
			return [
				// TODO: заполнить пользователем
				// [name, baseSpace, baseDir, type, auto, expected]
				['My\\Project', null, PATH_CLASSES, 'business', false, PATH_CLASSES . 'My' . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'Business' . DIRECTORY_SEPARATOR],
				['My\\Project', null, PATH_CLASSES, 'proto', true, PATH_CLASSES . 'My' . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'Auto' . DIRECTORY_SEPARATOR . 'Proto' . DIRECTORY_SEPARATOR],
			];
		}

		public function testFromXmlArray()
		{
			$attributes = [
				'namespace' => 'My\\Project',
				'base-space' => 'Vendor',
				'base-dir' => '/custom/path',
			];
			$ns = MetaNamespace::fromXmlArray($attributes);
			$this->assertEquals('My\\Project', $ns->getName());
			$this->assertEquals('Vendor', $ns->getBaseSpace());
			$this->assertEquals('/custom/path', $ns->getBaseDir());
		}
	}
?>
<?php

	final class PrimitiveEnumerationTest extends TestCase
	{
		public function testIntegerValues()
		{
			$form =
				Form::create()->
				add(
					Primitive::enumeration('enum')->of('DataType')
				);

			$form->import(['enum' => '4097']);

			$this->assertEquals($form->getValue('enum')->getId(), 0x001001);
			$this->assertSame($form->getValue('enum')->getId(), 0x001001);
		}

		public function testGetList()
		{
			$primitive = Primitive::enumeration('enum')->of('DataType');
			$enum = DataType::create(DataType::getAnyId());

			$this->assertEquals($primitive->getList(), $enum->getObjectList());

			$primitive->setDefault($enum);
			$this->assertEquals($primitive->getList(), $enum->getObjectList());

			$primitive->import(['enum' => DataType::getAnyId()]);
			$this->assertEquals($primitive->getList(), $enum->getObjectList());
		}
	}

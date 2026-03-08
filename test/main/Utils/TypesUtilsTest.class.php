<?php

	final class TypesUtilsTest extends TestCase
	{
		/**
		 * @dataProvider integers
		**/
		public function testSignedToUnsigned($signed, $unsigned)
		{
			$this->assertEquals(TypesUtils::signedToUnsigned($signed), $unsigned);
		}

		/**
		 * @dataProvider integers
		**/
		public function testUnsignedToSigned($signed, $unsigned)
		{
			$this->assertEquals($signed, TypesUtils::unsignedToSigned($unsigned));
		}

		public static function integers()
		{
			return
				[
					// signed, unsigned
					['-926365496', '3368601800'],
					['16843009', '16843009']
				];
		}
	}

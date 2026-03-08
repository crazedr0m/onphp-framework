<?php

	final class PrimitiveTimeTest extends TestCase
	{
		public function testImport()
		{
			$prm =
				Primitive::time('test')->
					setSingle(true)->
					setMax(Time::create('00:12:00'))->
					setMin(Time::create('00:10:00'));

			$array =
				 [
					0 => ['test' => '00:12:01'],
					1 => ['test' => '00:14:00'],
					2 => ['test' => '00:09:59'],
					3 => ['test' => '00:11:00'],
					4 => ['test' => '00:10:00'],
					5 => ['test' => '00:12:00'],
				];

			$this->assertFalse($prm->import($array[0]));
			$this->assertFalse($prm->import($array[1]));
			$this->assertFalse($prm->import($array[2]));
			$this->assertTrue($prm->import($array[3]));
			$this->assertTrue($prm->import($array[4]));
			$this->assertTrue($prm->import($array[5]));
		}
	}

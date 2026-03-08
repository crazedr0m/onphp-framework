<?php

	final class DateUtilsTest extends PHPUnit_Framework_TestCase
	{
		/**
		 * @dataProvider alignToSecondsDataProvider
		**/
		public function testAlignToSeconds(Timestamp $stamp, $expected)
		{
			$this->assertEquals(
				DateUtils::alignToSeconds($stamp, 42)->toString(),
				$expected
			);
		}

		public static function alignToSecondsDataProvider()
		{
			return [
				[
					Timestamp::create('2009-01-01 10:00:42'),
					'2009-01-01 10:00:42'
				],
				[
					Timestamp::create('2009-01-01 10:00:41'),
					'2009-01-01 10:00:00'
				],
				[
					Timestamp::create('2009-01-01 10:01:34'),
					'2009-01-01 10:01:24'
				],
				[
					Timestamp::create('2009-01-01 10:10:01'),
					'2009-01-01 10:09:48'
				]
			];
		}
	}

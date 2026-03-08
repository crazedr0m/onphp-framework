<?php

	final class RangeTest extends TestCase
	{
		/**
		 * @dataProvider rangeDataProvider
		**/
		public function testCreation($min, $max, $throwsException)
		{
			if ($throwsException) {
				$this->setExpectedException('WrongArgumentException');
            }

			$range = Range::create($min, $max);
		}

		public static function rangeDataProvider()
		{
			return [
				[
					1, 1, false
				],
				[
					1, 222222222222222222222222222, true
				],
				[
					0.1, 1, true
				],
				[
					0, 1, false
				],
				[
					2, 1, false
				]
			];
		}
	}

<?php

/***************************************************************************
 *   Copyright (C) 2011 by Evgeniy N. Sokolov                              *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	final class MathUtilsTest extends TestCase
	{
		public function testCompareFloat()
		{
			$this->assertEquals(
				MathUtils::compareFloat(0.001, 0.001),
				0
			);

			$this->assertEquals(
				MathUtils::compareFloat(0, 0.0001, 0.001),
				0
			);

			$this->assertEquals(
				MathUtils::compareFloat(0.0001, 0.00001, 0.000001),
				1
			);
		}

		public function testMatrixMultiplication()
		{
			$left = [
				[1, 2],
				[3, 5],
				[2, 4]
			];

			$right = [
				[4, 7, 5],
				[3, 1, 4]
			];

			$this->assertEquals(
				MathUtils::getMmult($left, $right),
				[
					[10, 9, 13],
					[27, 26, 35],
					[20, 18, 26],
				]
			);

			try {
				MathUtils::getMmult([], $right);
				$this->fail("Exception expected here");
			} catch (WrongArgumentException $e) {
            }

			try {
				MathUtils::getMmult($left, []);
				$this->fail("Exception expected here");
			} catch (WrongArgumentException $e) {
            }
		}
	}

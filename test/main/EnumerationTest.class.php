<?php

	final class EnumerationTest extends TestCase
	{
		public function testAnyId()
		{
			foreach (get_declared_classes() as $className) {
				if (is_subclass_of($className, 'Enumeration')) {
					try {
						$enum = new $className(
							call_user_func(
								[$className, 'getAnyId']
							)
						);

						/* pass */
					} catch (MissingElementException $e) {
						$this->fail($className);
					}
				} elseif (is_subclass_of($className, 'Enum')) {
					try {
						$enum = new $className(
							ClassUtils::callStaticMethod($className . '::getAnyId')
						);

						/* pass */
					} catch (MissingElementException $e) {
						$this->fail($className);
					}
				}
			}
		}
	}

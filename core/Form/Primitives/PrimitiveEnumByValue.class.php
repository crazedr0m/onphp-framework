<?php
/***************************************************************************
 *   Copyright (C) 2012 by Georgiy T. Kutsurua                             *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * @ingroup Primitives
	**/
	final class PrimitiveEnumByValue extends PrimitiveEnum
	{
		public function import($scope)
		{
			if (!$this->className) {
				throw new WrongStateException(
					"no class defined for PrimitiveEnum '{$this->name}'"
				);
			}

			Assert::isInstance($this->className, Enum::class);

			if (isset($scope[$this->name])) {
				$scopedValue = urldecode($scope[$this->name]);
				$this->raw = $scopedValue;

				$names = ClassUtils::callStaticMethod($this->className.'::getNameList');
				foreach ($names as $key => $value) {

					if ($value == $scopedValue) {
						try {
							$this->value = new $this->className($key);
							$this->imported = true;
						} catch (MissingElementException $e) {
							$this->value = null;
							return false;
						}
						return true;
					}
				}
				
				return false;
			}
			
			return null;
		}
	}
?>
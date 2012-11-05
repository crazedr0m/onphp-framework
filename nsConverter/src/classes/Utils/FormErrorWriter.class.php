<?php

/* * *************************************************************************
 *   Copyright (C) 2012 by Alexey Denisov                                  *
 *   alexeydsov@gmail.com                                                  *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 * ************************************************************************* */

namespace Onphp\NsConverter;

trait FormErrorWriter
{
	use OutputMsg;
	
	public function processFormError(\Onphp\Form $form)
	{
		if ($errors = $form->getErrors()) {
			$this->msg(print_r($errors, true));
			return true;
		}
	}
}

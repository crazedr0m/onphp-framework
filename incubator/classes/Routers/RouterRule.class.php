<?php
/***************************************************************************
 *   Copyright (C) 2008 by Sergey S. Sergeev                               *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	interface RouterRule
	{
		public function match(HttpRequest $request);
		
		public function assemble($data = array(), $reset = false, $encode = false);		
	}
?>
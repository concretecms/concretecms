<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Support\Facade\Facade;

class Set extends Facade
{

	public static function getFacadeAccessor()
	{
		return 'Concrete\Core\Attribute\SetFactory';
	}


}

<?php
namespace Concrete\Core\Support\Facade;
class Database extends Facade {

	public static function getFacadeAccessor() {return 'database';}
	public static function get() {
		return static::$app['database']->getActiveConnection();
	}

}
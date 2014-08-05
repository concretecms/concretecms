<?php
namespace Concrete\Core\Config;
use \Concrete\Core\Foundation\Object;
class ConfigValue extends Object {
	
	public $value;
	public $timestamp; // datetime value was set
	public $key;
}
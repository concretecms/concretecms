<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

interface AttributeKeyFactoryInterface
{

	public function bind($type, $class);
	public function make($type);

}

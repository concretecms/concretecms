<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class AttributeKeyFactory implements AttributeKeyFactoryInterface
{

	protected $application;
	protected $types;

	public function __construct(Application $application)
	{
		$this->application = $application;
	}

	public function bind($type, $class)
	{
		$this->types[$type] = $class;
	}

	public function make($type)
	{
		return $this->application->make($this->types[$type]);
	}

}

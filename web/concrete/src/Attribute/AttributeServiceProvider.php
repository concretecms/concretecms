<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class AttributeServiceProvider extends ServiceProvider
{

	public function register()
	{
		$app = $this->app;
		$this->app->bindShared('Concrete\Core\Attribute\AttributeKeyFactory', function() use ($app) {
			/** @var $factory \Concrete\Core\Attribute\AttributeKeyFactory */
			$factory = new AttributeKeyFactory($app);
			$factory->bind('text', 'Concrete\Core\Entity\AttributeKey\TextAttributeKey');
			$factory->bind('textarea', 'Concrete\Core\Entity\AttributeKey\TextareaAttributeKey');
			return $factory;
		});

	}

}

<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class AttributeServiceProvider extends ServiceProvider
{

	public function register()
	{
		$app = $this->app;
		$this->app->bindShared('attribute.factory', function() use ($app) {
			/** @var $factory \Concrete\Core\Attribute\AttributeKeyFactoryInterface */
			$factory = $app->make('Concrete\Core\Attribute\AttributeKeyFactory');
			$factory->bind('text', 'Concrete\Core\Entity\AttributeKey\TextAttributeKey');
			$factory->bind('text_area', 'Concrete\Core\Entity\AttributeKey\TextAreaAttributeKey');
			return $factory;
		});
		$this->app->bind('Concrete\Core\Attribute\AttributeKeyFactoryInterface', function() use ($app) {
			return $app->make('attribute.factory');
		});

	}

}

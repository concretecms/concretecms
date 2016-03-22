<?php

namespace Concrete\Core\Localization;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory as CoreTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory as PlainTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory as ZendTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepository;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;

class LocalizationEssentialServiceProvider extends ServiceProvider
{

    /**
     * Services that are essential for the loading of the localization
     * functionality.
     */
    public function register()
    {
        if (!$this->app->bound('Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface')) {
            $this->app->bind('Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface', function ($app, $params) {
                $config = $app->make('config');
                $loaders = $config->get('i18n.adapters.zend.loaders', array());

                $loaderRepository = new TranslationLoaderRepository();
                foreach ($loaders as $key => $class) {
                    $loader = $app->build($class, array($app));
                    $loaderRepository->registerTranslationLoader($key, $loader);
                }

                $zendFactory = new ZendTranslatorAdapterFactory($loaderRepository);
                $plainFactory = new PlainTranslatorAdapterFactory();

                return new CoreTranslatorAdapterFactory($config, $plainFactory, $zendFactory);
            });
        }

        $this->app->bindShared('Concrete\Core\Localization\Localization', function ($app) {
            $loc = new Localization();

            $translatorAdapterFactory = $app->make('Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface');
            $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
            $loc->setTranslatorAdapterRepository($repository);

            $loc->setActiveContext('system');

            return $loc;
        });
    }

}

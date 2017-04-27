<?php
namespace Concrete\Core\Localization;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory as CoreTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory as PlainTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory as ZendTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepository;
use Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;

class LocalizationEssentialServiceProvider extends ServiceProvider
{
    /**
     * Services that are essential for the loading of the localization
     * functionality.
     */
    public function register()
    {
        if (!$this->app->bound(TranslatorAdapterFactoryInterface::class)) {
            $this->app->bind(TranslatorAdapterFactoryInterface::class, function ($app, $params) {
                $config = $app->make('config');
                $loaders = $config->get('i18n.adapters.zend.loaders', []);

                $loaderRepository = new TranslationLoaderRepository();
                foreach ($loaders as $key => $class) {
                    $loader = $app->build($class, [$app]);
                    $loaderRepository->registerTranslationLoader($key, $loader);
                }

                $zendFactory = new ZendTranslatorAdapterFactory($loaderRepository);
                $plainFactory = new PlainTranslatorAdapterFactory();

                return new CoreTranslatorAdapterFactory($config, $plainFactory, $zendFactory);
            });
        }

        $this->app->singleton(Localization::class, function ($app) {
            $loc = new Localization();

            $translatorAdapterFactory = $app->make(TranslatorAdapterFactoryInterface::class);
            $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
            $loc->setTranslatorAdapterRepository($repository);

            $loc->setActiveContext(Localization::CONTEXT_UI);

            return $loc;
        });
    }
}

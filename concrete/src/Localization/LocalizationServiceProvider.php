<?php
namespace Concrete\Core\Localization;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Localization\Translation\Local\Factory as LocalTranslationsFactory;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalTranslationsFactoryInterface;
use Concrete\Core\Localization\Translation\Remote\CommunityStoreTranslationProvider;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteTranslationsProviderInterface;

class LocalizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'Concrete\Core\Localization\Service\CountryList' => [
                'helper/localization/countries',
                'helper/lists/countries',
                'localization/countries',
                'lists/countries',
            ],
            'Concrete\Core\Localization\Service\StatesProvincesList' => [
                'helper/localization/states_provinces',
                'helper/lists/states_provinces',
                'localization/states_provinces',
                'lists/states_provinces',
            ],
            'Concrete\Core\Localization\Service\Date' => [
                'helper/date',
                'date',
            ],
            'Concrete\Core\Localization\Service\LanguageList' => [
                'localization/languages',
            ],
        ];

        foreach ($singletons as $class => $aliases) {
            $this->app->singleton($class);
            foreach ($aliases as $alias) {
                $this->app->alias($class, $alias);
            }
        }

        if (!$this->app->bound(LocalTranslationsFactoryInterface::class)) {
            $this->app->alias(LocalTranslationsFactory::class, LocalTranslationsFactoryInterface::class);
        }
        $this->app->bind(LocalTranslationsFactory::class, function ($app) {
            return $app->build(LocalTranslationsFactory::class, ['cache' => $app->make('cache/expensive')]);
        });
        if (!$this->app->bound(RemoteTranslationsProviderInterface::class)) {
            $this->app->alias(CommunityStoreTranslationProvider::class, RemoteTranslationsProviderInterface::class);
        }
        $this->app->bind(CommunityStoreTranslationProvider::class, function ($app) {
            return $app->build(CommunityStoreTranslationProvider::class, ['cache' => $app->make('cache/expensive')]);
        });
    }
}

<?php
namespace Concrete\Core\Localization;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Client\Factory;
use Concrete\Core\Localization\Service\AddressFormat;
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

        $this->app->bindIf(LocalTranslationsFactoryInterface::class, function($app) {
            return $app->make(LocalTranslationsFactory::class, ['cache' => $app->make('cache/expensive')]);
        });
        $this->app->bindIf(RemoteTranslationsProviderInterface::class, function($app) {
            return new CommunityStoreTranslationProvider(
                $app->make('config'),
                $app->make('cache'),
                $app->make(Factory::class)
            );
        });
        $this->app->singleton(AddressFormat::class);
    }
}

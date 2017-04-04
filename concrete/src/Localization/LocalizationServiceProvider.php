<?php
namespace Concrete\Core\Localization;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

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
    }
}

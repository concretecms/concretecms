<?php

namespace Concrete\Core\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Form\Context\Registry\ControlRegistry;
use Concrete\Core\Form\Service\DestinationPicker;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Request;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/form' => '\Concrete\Core\Form\Service\Form',
            'helper/form/attribute' => '\Concrete\Core\Form\Service\Widget\Attribute',
            'helper/form/color' => '\Concrete\Core\Form\Service\Widget\Color',
            'helper/form/font' => '\Concrete\Core\Form\Service\Widget\Typography',
            'helper/form/typography' => '\Concrete\Core\Form\Service\Widget\Typography',
            'helper/form/date_time' => '\Concrete\Core\Form\Service\Widget\DateTime',
            'helper/form/page_selector' => '\Concrete\Core\Form\Service\Widget\PageSelector',
            'helper/form/rating' => '\Concrete\Core\Form\Service\Widget\Rating',
            'helper/form/user_selector' => '\Concrete\Core\Form\Service\Widget\UserSelector',
            'helper/form/group_selector' => '\Concrete\Core\Form\Service\Widget\GroupSelector',
            'form/express/entry_selector' => '\Concrete\Core\Form\Service\Widget\ExpressEntrySelector',
        ];

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->singleton(ControlRegistry::class);

        $this->app->bind(DestinationPicker\DestinationPicker::class, function (Application $app) {
            $request = Request::createFromGlobals();
            $picker = new DestinationPicker\DestinationPicker($app, $app->make(Form::class), $request);
            $picker->registerPickers([
                'none' => $app->make(DestinationPicker\NoDestinationPicker::class),
                'page' => $app->make(DestinationPicker\PagePicker::class),
                'file' => $app->make(DestinationPicker\FilePicker::class),
                'external_url' => $app->make(DestinationPicker\ExternalUrlPicker::class),
                'email' => $app->make(DestinationPicker\EmailPicker::class)
            ]);
            return $picker;
        });
    }
}

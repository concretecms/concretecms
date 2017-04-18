<?php
namespace Concrete\Core\Form;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Form\Context\Registry\ControlRegistry;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = array(
            'helper/form' => '\Concrete\Core\Form\Service\Form',
            'helper/form/attribute' => '\Concrete\Core\Form\Service\Widget\Attribute',
            'helper/form/color' => '\Concrete\Core\Form\Service\Widget\Color',
            'helper/form/font' => '\Concrete\Core\Form\Service\Widget\Typography',
            'helper/form/typography' => '\Concrete\Core\Form\Service\Widget\Typography',
            'helper/form/date_time' => '\Concrete\Core\Form\Service\Widget\DateTime',
            'helper/form/page_selector' => '\Concrete\Core\Form\Service\Widget\PageSelector',
            'helper/form/rating' => '\Concrete\Core\Form\Service\Widget\Rating',
            'helper/form/user_selector' => '\Concrete\Core\Form\Service\Widget\UserSelector',
            'form/express/entry_selector' => '\Concrete\Core\Form\Service\Widget\ExpressEntrySelector',

        );

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->singleton(ControlRegistry::class);
    }
}

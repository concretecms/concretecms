<?php
namespace Concrete\Core\Html;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class HtmlServiceProvider extends ServiceProvider {

    public function register() {
        $singletons = array(
            'helper/html' => '\Concrete\Core\Html\Service\Html',
            'helper/overlay' => '\Concrete\Core\Html\Service\Overlay',
            'helper/navigation' => '\Concrete\Core\Html\Service\Navigation',

            'html/image' => '\Concrete\Core\Html\Image'
        );

        foreach($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }
    }


}
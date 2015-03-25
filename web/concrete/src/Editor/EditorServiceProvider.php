<?php
namespace Concrete\Core\Editor;

use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('editor', '\Concrete\Core\Editor\RedactorEditor');
    }


}
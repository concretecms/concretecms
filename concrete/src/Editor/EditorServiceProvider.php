<?php
namespace Concrete\Core\Editor;

use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bindShared('editor', function() {
            return new \Concrete\Core\Editor\RedactorEditor();
        });
    }


}
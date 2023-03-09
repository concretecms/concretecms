<?php

namespace Concrete\Core\ImageEditor;

use Concrete\Core\Foundation\Service\Provider;

class ImageEditorServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
    }
}

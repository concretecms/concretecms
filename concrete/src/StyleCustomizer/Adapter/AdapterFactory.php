<?php

namespace Concrete\Core\StyleCustomizer\Adapter;

use Concrete\Core\Application\Application;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class AdapterFactory
{

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, FileLocator $fileLocator)
    {
        $this->app = $app;
        $this->fileLocator = $fileLocator;
    }

    /**
     * When given a particular theme, returns the adapter that ought to be used with it.
     *
     * @param Theme $theme
     */
    public function createFromTheme(Theme $theme)
    {
        if ($theme->getPackageHandle()) {
            $this->fileLocator->addPackageLocation($theme->getPackageHandle());
        }
        $record = $this->fileLocator->getRecord(
            DIRNAME_THEMES .'/' .
            $theme->getThemeHandle() . '/' .
            DIRNAME_SCSS
        );

        if ($record->exists()) {
            return $this->app->make(ScssAdapter::class);
        }

        return $this->app->make(LessAdapter::class);
    }


}

<?php

namespace Concrete\Core\Page\Container;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\FileLocator\Record;
use Concrete\Core\Filesystem\TemplateVariantLocator;
use Concrete\Core\Page\Page;

/**
 * Responsible for locating and rendering templates in a theme.
 */
class TemplateLocator
{

    /**
     * @var FileLocator 
     */
    protected $fileLocator;

    /**
     * @var FileLocator\ThemeLocation
     */
    protected $themeLocation;
    
    public function __construct(FileLocator $fileLocator, FileLocator\ThemeLocation $themeLocation)
    {
        $this->fileLocator = $fileLocator;
        $this->themeLocation = $themeLocation;
    }

    /**
     * @param Page $page
     * @param Container $container
     * @return string file
     */
    public function getFileToRender(Page $page, Container $container, bool $template = false)
    {
        $theme = $page->getCollectionThemeObject();
        if ($theme) {
            $handle = $container->getContainerHandle();
            if ($handle) {
                $filename = DIRNAME_ELEMENTS . '/' . DIRNAME_CONTAINERS . '/' . $handle . '.php';
                $this->themeLocation->setTheme($theme);
                $this->fileLocator->addLocation($this->themeLocation);

                // A container may also be (or exclusively be) provided by a package, e.g.
                // {package}/elements/containers/{handle}.php. Add the package location in
                // addition to the theme location so both are searched.
                $pkgHandle = $container->getPackageHandle();
                if ($pkgHandle) {
                    $this->fileLocator->addPackageLocation($pkgHandle);
                }

                $record = $template
                    ? (new TemplateVariantLocator($this->fileLocator))->getRecord($filename)
                    : $this->fileLocator->getRecord($filename);
                if ($record->exists()) {
                    return $record->getFile();
                }
            }
        }
        
        return null;

    }
    
}
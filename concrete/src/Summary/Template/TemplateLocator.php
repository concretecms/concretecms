<?php

namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;

/**
 * Responsible for locating and rendering summary templates.
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

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(Service $siteService, FileLocator $fileLocator, FileLocator\ThemeLocation $themeLocation)
    {
        $this->siteService = $siteService;
        $this->fileLocator = $fileLocator;
        $this->themeLocation = $themeLocation;
    }

    /**
     * @return FileLocator
     */
    public function getFileLocator()
    {
        return $this->fileLocator;
    }

    /**
     * @param Page $page
     * @param Template $template
     * @return string file
     */
    public function getFileToRender(Template $template)
    {
        $site = $this->siteService->getSite();
        if ($site) {
            $theme = $site->getSiteHomePageObject()->getCollectionThemeObject();
            if ($theme) {
                $handle = $template->getHandle();
                if ($handle) {
                    $filename = DIRNAME_ELEMENTS . '/' . DIRNAME_SUMMARY . '/' . DIRNAME_SUMMARY_TEMPLATES . '/' . $handle . '.php';
                    $this->themeLocation->setTheme($theme);
                    $this->fileLocator->addLocation($this->themeLocation);
                    if ($template->getPackageHandle()) {
                        $this->fileLocator->addPackageLocation($template->getPackageHandle());
                    }
                    $record = $this->fileLocator->getRecord($filename);
                    if ($record->exists()) {
                        return $record->getFile();
                    }
                }
            }
        }

        return null;

    }

}

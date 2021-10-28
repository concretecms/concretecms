<?php

namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;

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
     * @param Page $page
     * @param SlotTemplate $template
     * @return string file
     */
    public function getFileToRender(SlotTemplate $template)
    {
        $site = $this->siteService->getSite();
        if ($site) {
            $theme = $site->getSiteHomePageObject()->getCollectionThemeObject();
            if ($theme) {
                $handle = $template->getHandle();
                if ($handle) {
                    $filename = DIRNAME_ELEMENTS . '/' . DIRNAME_BOARDS . '/' . DIRNAME_BOARD_SLOTS . '/' . $handle . '.php';
                    $this->themeLocation->setTheme($theme);
                    $this->fileLocator->addLocation($this->themeLocation);
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

<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Service;

class ResetSiteThemeLegacyCustomizationsCommandHandler
{

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Service $siteService, Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(ResetSiteThemeLegacyCustomizationsCommand $command)
    {
        $activeTheme = Theme::getSiteTheme();
        $this->db->delete('PageThemeCustomStyles', ['pThemeID' => $activeTheme->getThemeID()]);
        $sheets = $activeTheme->getThemeCustomizableStyleSheets();
        foreach ($sheets as $sheet) {
            $sheet->clearOutputFile();
        }
    }

}
<?php
namespace Concrete\Core\StyleCustomizer\Customizations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Theme\Command\ResetPageThemeLegacyCustomizationsCommand;
use Concrete\Core\Page\Theme\Command\ResetSiteThemeLegacyCustomizationsCommand;
use Concrete\Core\Site\Tree\Traits\GetTreeIdsForQueryTrait;

class LegacyCustomizationsManager implements ManagerInterface
{

    use GetTreeIdsForQueryTrait;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function hasPageThemeCustomizations(Site $site): bool
    {
        $treeIDs = $this->getTreeIdsForQuery($site);
        $customStylePageRecords = $this->db->fetchOne("select count(CollectionVersionThemeCustomStyles.cID) from CollectionVersionThemeCustomStyles inner join Pages on (CollectionVersionThemeCustomStyles.cID = Pages.cID) where Pages.siteTreeID in ({$treeIDs})");
        if ($customStylePageRecords > 0) {
            return true;
        }
        return false;
    }

    public function hasSiteThemeCustomizations(Site $site): bool
    {
        $customPageThemeRecords = $this->db->fetchOne("select count(PageThemeCustomStyles.scvlID) from PageThemeCustomStyles");
        if ($customPageThemeRecords > 0) {
            return true;
        }
        return false;
    }

    public function getResetPageThemeCustomizationsCommand(Site $site)
    {
        return new ResetPageThemeLegacyCustomizationsCommand($site);
    }

    public function getResetSiteThemeCustomizationsCommand(Site $site)
    {
        return new ResetSiteThemeLegacyCustomizationsCommand();
    }

}

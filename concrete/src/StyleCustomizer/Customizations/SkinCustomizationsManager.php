<?php
namespace Concrete\Core\StyleCustomizer\Customizations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Theme\Command\ResetPageThemeSkinsCommand;
use Concrete\Core\Page\Theme\Command\ResetSiteThemeSkinCommand;
use Concrete\Core\Site\Tree\Traits\GetTreeIdsForQueryTrait;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class SkinCustomizationsManager implements ManagerInterface
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
        $customSkinPageRecords = $this->db->fetchOne("select count(CollectionVersions.cID) from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID) where Pages.siteTreeID in ({$treeIDs}) and CollectionVersions.pThemeSkinIdentifier is not null");
        if ($customSkinPageRecords > 0) {
            return true;
        }
        return false;
    }

    public function hasSiteThemeCustomizations(Site $site): bool
    {
        $skinIdentifier = $site->getThemeSkinIdentifier();
        if ($skinIdentifier && $skinIdentifier !== SkinInterface::SKIN_DEFAULT) {
            return true;
        }
        return false;
    }

    public function getResetPageThemeCustomizationsCommand(Site $site)
    {
        return new ResetPageThemeSkinsCommand($site);
    }

    public function getResetSiteThemeCustomizationsCommand(Site $site)
    {
        return new ResetSiteThemeSkinCommand($site);
    }



}

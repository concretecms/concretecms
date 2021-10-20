<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Site\Service;
use Concrete\Core\Site\Tree\Traits\GetTreeIdsForQueryTrait;

class ResetPageThemeLegacyCustomizationsCommandHandler
{

    use GetTreeIdsForQueryTrait;

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(ResetPageThemeSkinsCommand $command)
    {
        $site = $command->getSite();
        $treeIDs = $this->getTreeIdsForQuery($site);
        $this->db->executeStatement("delete CollectionVersionThemeCustomStyles from CollectionVersionThemeCustomStyles inner join Pages on (CollectionVersionThemeCustomStyles.cID = Pages.cID) where Pages.siteTreeID in ({$treeIDs})");
    }


}
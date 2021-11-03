<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Site\Service;
use Concrete\Core\Site\Tree\Traits\GetTreeIdsForQueryTrait;

class ResetPageThemeSkinsCommandHandler
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
        $this->db->executeStatement("update CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID) set pThemeSkinIdentifier = null where Pages.siteTreeID in ({$treeIDs})");
    }


}
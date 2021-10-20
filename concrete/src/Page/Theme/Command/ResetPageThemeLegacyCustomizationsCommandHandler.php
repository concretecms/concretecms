<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Command\ClearCacheCommand;
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

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, Connection $db)
    {
        $this->app = $app;
        $this->db = $db;
    }

    public function __invoke(ResetPageThemeLegacyCustomizationsCommand $command)
    {
        $site = $command->getSite();
        $treeIDs = $this->getTreeIdsForQuery($site);
        $this->db->executeStatement("delete CollectionVersionThemeCustomStyles from CollectionVersionThemeCustomStyles inner join Pages on (CollectionVersionThemeCustomStyles.cID = Pages.cID) where Pages.siteTreeID in ({$treeIDs})");
        $command = new ClearCacheCommand();
        $this->app->executeCommand($command);

    }


}
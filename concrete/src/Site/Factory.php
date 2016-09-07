<?php
namespace Concrete\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Site\Config\Liaison;
use Doctrine\ORM\EntityManagerInterface;

class Factory
{

    protected $config;

    public function __construct(\Illuminate\Config\Repository $configRepository)
    {
        $this->config = $configRepository;
    }

    /**
     * Either creates a completely new entity, or ensures that the passed entity has all the items it
     * needs to function (e.g. a config repository)
     * @param Site|null $site
     */
    public function createEntity(Site $site = null)
    {
        if (!$site) {
            $site = new Site($this->config);
        } else {
            $site->updateSiteConfigRepository($this->config);
        }
        return $site;
    }

    public function createDefaultEntity()
    {
        $tree = new Tree();
        $tree->setSiteHomePageID(HOME_CID);

        $site = new Site($this->config);
        $site->setSiteHandle('default');
        $site->setIsDefault(true);
        $site->setSiteTree($tree);

        return $site;
    }
}

<?php
namespace Concrete\Core\Permission\Registry\Multisite\Entry\Access\Entity;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\Access\Entity\SiteGroupEntity as SiteGroupAccessEntity;

class SiteGroupInstanceEntity extends SiteGroupEntity
{

    protected $site;

    public function __construct(Site $site, $groupName)
    {
        parent::__construct($site->getType(), $groupName);
        $this->site = $site;
    }

    public function getAccessEntity()
    {
        /**
         * @var $entity SiteGroupAccessEntity
         */
        $entity = parent::getAccessEntity();
        if (is_object($entity)) {
            return $entity->getInstanceGroup($this->site);
        }

    }


}

<?php
namespace Concrete\Core\Permission\Registry\Multisite\Entry\Access\Entity;

use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;
use Concrete\Core\Permission\Access\Entity\SiteGroupEntity as SiteGroupAccessEntity;

class SiteGroupEntity implements EntityInterface
{

    protected $type;
    protected $groupName;

    public function __construct($type, $groupName)
    {
        $this->type = $type;
        $this->groupName = $groupName;
    }

    public function getAccessEntity()
    {
        $type = $this->type;
        if (!is_object($type)) {
            $type = \Core::make("site/type")->getByHandle($type);
        }
        $em = \ORM::entityManager();
        $r = $em->getRepository('\PortlandLabs\Liberta\Entity\Site\Group\Group');
        $group = $r->findOneBy(['type' => $type, 'groupName' => $this->groupName]);
        $entity =  SiteGroupAccessEntity::getOrCreate($group);
        return $entity;
    }

}

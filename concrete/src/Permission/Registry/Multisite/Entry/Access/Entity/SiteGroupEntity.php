<?php
namespace Concrete\Core\Permission\Registry\Multisite\Entry\Access\Entity;

use Concrete\Core\Entity\Site\Group\Group;
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface::getAccessEntity()
     *
     * @return \Concrete\Core\Permission\Access\Entity\SiteGroupEntity|null NULL if the site_group access entity type isn't installed
     */
    public function getAccessEntity()
    {
        $type = $this->type;
        if (!is_object($type)) {
            $type = \Core::make("site/type")->getByHandle($type);
        }
        $em = \ORM::entityManager();
        $r = $em->getRepository(Group::class);
        $group = $r->findOneBy(['type' => $type, 'groupName' => $this->groupName]);
        $entity =  SiteGroupAccessEntity::getOrCreate($group);
        return $entity;
    }

}

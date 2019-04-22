<?php
namespace Concrete\Core\Site\User\Group;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\User\Group\Group;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Site\Group\Relation;
use Concrete\Core\Entity\Site\Group\Group as SiteGroupEntity;

class Service
{

    const PARENT_GROUP_PATH = '/Sites';
    const PARENT_GROUP_NAME = 'Sites';

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSiteParentGroup()
    {
        return Group::getByPath(static::PARENT_GROUP_PATH);
    }

    public function getSiteTypeGroups(Type $type)
    {
        return $this->entityManager->getRepository(SiteGroupEntity::class)
            ->findByType($type);
    }

    public function getInstanceGroupsBySite(Site $site)
    {
        $relations = $this->entityManager->getRepository(Relation::class)
            ->findBySite($site);
        $groups = array();
        foreach($relations as $relation) {
            $groups[] = Group::getByID($relation->getInstanceGroupID());
        }
        return $groups;
    }

    public function addGroup(Type $type, $groupName)
    {
        $group = new SiteGroupEntity();
        $group->setSiteGroupName($groupName);
        $group->setSiteType($type);
        $this->entityManager->persist($group);
        $this->entityManager->flush();
    }


}

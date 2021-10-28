<?php

namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Entity\Permission\SiteGroup;
use Concrete\Core\Entity\Site\Group\Group;
use Concrete\Core\Entity\Site\Group\Relation;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\SiteAccessInterface;
use Concrete\Core\Permission\Access\WorkflowAccess;
use Concrete\Core\Workflow\Progress\SiteProgressInterface;

class SiteGroupEntity extends Entity
{
    /**
     * @var SiteGroup
     */
    protected $siteGroup;

    /**
     * @param WorkflowAccess $pa
     *
     * @return array
     */
    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $wp = $pa->getWorkflowProgressObject();
        if ($wp instanceof SiteProgressInterface) {
            $site = $wp->getSite();
        } else {
            $site = \Core::make('site')->getSite();
        }
        if ($site) {
            $group = $this->getInstanceGroup($site);
            if (is_object($group)) {
                return $group->getGroupMembers();
            }
        }

        return [];
    }

    /**
     * @return SiteGroup
     */
    public function getSiteGroupObject()
    {
        return $this->siteGroup;
    }

    public function getInstanceGroup(Site $site)
    {
        // Retrieve the corresponding group relation for this site group + site
        $em = \Database::connection()->getEntityManager();
        $relation = $em->getRepository(Relation::class)
            ->findOneBy(['site' => $site, 'group' => $this->siteGroup->getSiteGroup()]);

        if ($relation) {
            return \Concrete\Core\User\Group\Group::getByID($relation->getInstanceGroupID());
        }
    }

    public function validate(PermissionAccess $pae)
    {
        if ($pae instanceof SiteAccessInterface) {
            $site = $pae->getSite();
        }

        if (!$site) {
            $site = \Core::make('site')->getActiveSiteForEditing();
        }

        $cache = \Core::make('cache/request');
        $item = $cache->getItem(sprintf('site_group/%s/%s', $site->getSiteID(), $this->siteGroup->getSiteGroupEntityID()));
        if (!$item->isMiss()) {
            return $item->get();
        }
        $item->lock();
        $valid = false;
        $group = $this->getInstanceGroup($site);
        if ($group) {
            $u = \Core::make(\Concrete\Core\User\User::class);
            $valid = $u->inGroup($group);
        }

        $cache->save($item->set($valid));

        return $valid;
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="javascript:void(0)" class="dropdown-item" onclick="ccm_choosePermissionAccessEntitySiteGroup()">' . tc('PermissionAccessEntityTypeName', 'Multisite Group') . '</a>';

        return $html;
    }

    public static function getAccessEntitiesForUser($user)
    {
        // First, we get the groups the user is in.
        $groups = $user->getUserGroups();

        // Now, we determine which site groups those users derive from.
        $em = \Database::connection()->getEntityManager();
        $entities = [];
        foreach ($groups as $group) {
            $relations = $em->getRepository(Relation::class)
                ->findByGroupID($group);
            foreach ($relations as $relation) {
                $entity = $em->getRepository(SiteGroup::class)
                    ->findOneByGroup($relation->getSiteGroup());
                if (is_object($entity)) {
                    $accessEntity = \Concrete\Core\Permission\Access\Entity\Entity::getByID($entity->getPermissionAccessEntityID());
                    if (is_object($accessEntity)) {
                        $entities[] = $accessEntity;
                    }
                }
            }
        }

        return $entities;
    }

    public static function getOrCreate(Group $siteGroup)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();

        $petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'site_group\'');

        $r = $em->getRepository(SiteGroup::class);
        $siteGroupEntity = $r->findOneByGroup($siteGroup);

        if (!$siteGroupEntity) {
            $db->Execute('insert into PermissionAccessEntities (petID) values(?)', [$petID]);
            $peID = $db->Insert_ID();
            \Config::save('concrete.misc.access_entity_updated', time());

            $siteGroupEntity = new SiteGroup();
            $siteGroupEntity->setSiteGroup($siteGroup);
            $siteGroupEntity->setPermissionAccessEntityID($peID);
            $em->persist($siteGroupEntity);
            $em->flush();
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($siteGroupEntity->getPermissionAccessEntityID());
    }

    public function load()
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();
        $siteGroup = $em->getRepository(SiteGroup::class)
            ->findOneBy(['peID' => $this->peID]);
        if (is_object($siteGroup)) {
            $this->siteGroup = $siteGroup;
            if (is_object($this->siteGroup->getSiteGroup())) {
                $this->label = $this->siteGroup->getSiteGroup()->getSiteGroupName();
            }
        } else {
            $this->label = t('(Unknown Group)');
        }
    }
}

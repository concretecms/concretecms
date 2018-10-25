<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\User\UserList;
use Loader;
use Config;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Support\Facade\Facade;

class GroupCombinationEntity extends Entity
{
    protected $groups = array();

    public function getGroups()
    {
        return $this->groups;
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/permissions/dialogs/access/entity/types/group_combination" dialog-width="400" dialog-height="300" class="dialog-launch" dialog-modal="false" dialog-title="' . t('Add Group Combination') . '">' . tc('PermissionAccessEntityTypeName', 'Group Combination') . '</a>';

        return $html;
    }

    public static function getAccessEntitiesForUser($user)
    {
        // finally, the most brutal one. we find any combos that this group would specifically be in.
        // first, we look for any combos that contain any of the groups this user is in. That way if there aren't any we can just skip it.
        $db = Loader::db();
        $ingids = array();
        $db = Loader::db();
        foreach ($user->getUserGroups() as $key => $val) {
            $ingids[] = $key;
        }
        $instr = implode(',', $ingids);
        $entities = array();
        if ($user->isRegistered()) {
            $peIDs = $db->GetCol('select distinct pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityTypes paet on pae.petID = paet.petID inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where petHandle = \'group_combination\' and paeg.gID in (' . $instr . ')');
            // now for each one we check to see if it applies
            foreach ($peIDs as $peID) {
                $r = $db->GetRow('select count(gID) as peGroups, (select count(UserGroups.gID) from UserGroups where uID = ? and gID in (select gID from PermissionAccessEntityGroups where peID = ?)) as uGroups from PermissionAccessEntityGroups where peID = ?', array(
                    $user->getUserID(), $peID, $peID, ));
                if ($r['peGroups'] == $r['uGroups'] && $r['peGroups'] > 1) {
                    $entity = Entity::getByID($peID);
                    if (is_object($entity)) {
                        $entities[] = $entity;
                    }
                }
            }
        }

        return $entities;
    }

    /**
     * Function used to get or create a GroupCombination Permission Access Entity
     *
     * @param $groups Group[]
     * @return self
     */
    public static function getOrCreate($groups)
    {

        $app = Facade::getFacadeApplication();
        /** @var $database \Concrete\Core\Database\Connection\Connection */
        $database = $app->make('database')->connection();
        $petID = $database->fetchColumn('select petID from PermissionAccessEntityTypes where petHandle = \'group_combination\'');
        $query = $database->createQueryBuilder();
        $query->select('pae.peID')->from('PermissionAccessEntities', 'pae');
        $i = 1;
        $query->where('petid = :entityTypeID')->setParameter('entityTypeID',$petID);
        foreach ($groups as $group) {
            $query
                ->leftJoin('pae','PermissionAccessEntityGroups','paeg'.$i, 'pae.peID = paeg'.$i.'.peID')
                ->andWhere('paeg'.$i.'.gID = :group'.$i)
                ->setParameter('group'.$i, $group->getGroupID());
            ++$i;
        }

        $peIDs = $query->execute()->fetchAll();
        $peID = 0;

        // Check for all the groups belonging to this AccessEntity
        if (!empty($peIDs)) {
            foreach ($peIDs as $peID) {
                $allGroups = $database->fetchColumn('select count(gID) from PermissionAccessEntityGroups WHERE peID = '. $peID['peID']);
                if ($allGroups == count($groups)) {
                    $peID = $peID['peID'];
                    break;
                }
            }
        }
        // If the accessEntity doesnt exist then create a new one
        if (empty($peID)) {
            $database->insert('PermissionAccessEntities',['petID'=>$petID]);
            $peID = $database->lastInsertId();
            $app->make('config')->save('concrete.misc.access_entity_updated', time());
            foreach ($groups as $group) {
                $database->insert('PermissionAccessEntityGroups', ['peID'=>$peID, 'gID'=>$group->getGroupID()]);
            }
        }

        return self::getByID($peID);
    }

    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $gl = new UserList();
        $gl->ignorePermissions();
        foreach ($this->groups as $g) {
            $gl->filterByGroupID($g->getGroupID());
        }

        return $gl->get();
    }

    public function load()
    {
        $db = Loader::db();
        $gIDs = $db->GetCol('select gID from PermissionAccessEntityGroups where peID = ? order by gID asc', array($this->peID));
        if ($gIDs && is_array($gIDs)) {
            for ($i = 0; $i < count($gIDs); ++$i) {
                $g = Group::getByID($gIDs[$i]);
                if (is_object($g)) {
                    $this->groups[] = $g;
                    $this->label .= $g->getGroupDisplayName();
                    if ($i + 1 < count($gIDs)) {
                        $this->label .= t(' + ');
                    }
                }
            }
        }
    }
}

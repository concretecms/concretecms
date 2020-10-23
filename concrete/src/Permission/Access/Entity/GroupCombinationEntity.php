<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\User\UserList;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class GroupCombinationEntity extends Entity
{
    /**
     * Collection of groups belonging to this GroupCombinationEntity.
     *
     * @var Group[] | array
     */
    protected $groups = [];

    /**
     * Function to get the groups belonging to this GroupCombination.
     *
     * @return Group[] | array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Function that returns a html link for the model dialog to be launched from.
     *
     * @return string
     */
    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="' . h(app(ResolverManagerInterface::class)->resolve(['/ccm/system/dialogs/permissions/access/entity/types/group_combination'])) . '" '
            . 'dialog-width="400" dialog-height="300" class="dropdown-item dialog-launch" '
            . 'dialog-modal="false" dialog-title="'
            . t('Add Group Combination') . '">'
            . tc('PermissionAccessEntityTypeName', 'Group Combination')
            . '</a>';

        return $html;
    }

    /**
     * Returns all GroupCombination Access Entities for the provided user.
     *
     * @param \Concrete\Core\User\User | \Concrete\Core\Entity\User\User $user
     *
     * @return Entity[] | array
     */
    public static function getAccessEntitiesForUser($user)
    {
        $entities = [];
        // If the user isnt registered, there is no need to do this anymore.
        if ($user->isRegistered()) {
            $ingids = [];
            $app = Facade::getFacadeApplication();
            /** @var $database \Concrete\Core\Database\Connection\Connection */
            $database = $app->make('database')->connection();

            // First look for any combos that this group would specifically be in.
            // We check if the combos contain any of the groups this user is in.
            // That way if there aren't any we can just skip it.
            foreach ($user->getUserGroups() as $key => $val) {
                $ingids[] = $key;
            }
            $instr = implode(',', $ingids);

            $peIDs = $database->fetchAll(
                'select distinct pae.peID from PermissionAccessEntities pae
                inner join PermissionAccessEntityTypes paet on pae.petID = paet.petID
                inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID
                where petHandle = \'group_combination\' 
                and paeg.gID in (' . $instr . ')'
            );
            // Now for each one we check to see if it applies here.
            foreach ($peIDs as $peID) {
                $r = $database->fetchAssoc(
                    'select count(gID) as peGroups, 
                    (select count(UserGroups.gID) from UserGroups where uID = ? 
                      and gID in 
                      (select gID from PermissionAccessEntityGroups where peID = ?))
                     as uGroups from PermissionAccessEntityGroups where peID = ?',
                    [$user->getUserID(), $peID['peID'], $peID['peID']]
                );
                if ($r['peGroups'] == $r['uGroups'] && $r['peGroups'] > 1) {
                    $entity = Entity::getByID($peID['peID']);
                    if (is_object($entity)) {
                        $entities[] = $entity;
                    }
                }
            }
        }

        return $entities;
    }

    /**
     * Function used to get or create a GroupCombination Permission Access Entity.
     *
     * @param Group[] $groups Groups for this combination.
     *
     * @return self
     */
    public static function getOrCreate($groups)
    {
        $app = Facade::getFacadeApplication();
        /** @var $database \Concrete\Core\Database\Connection\Connection */
        $database = $app->make('database')->connection();
        $petID = $database->fetchColumn(
            'select petID from PermissionAccessEntityTypes
                      where petHandle = \'group_combination\''
        );
        $query = $database->createQueryBuilder();
        $query->select('pae.peID')->from('PermissionAccessEntities', 'pae');
        $i = 1;
        $query->where('petid = :entityTypeID')->setParameter('entityTypeID', $petID);
        foreach ($groups as $group) {
            $query
                ->leftJoin(
                    'pae',
                    'PermissionAccessEntityGroups',
                    'paeg' . $i,
                    'pae.peID = paeg' . $i . '.peID'
                )
                ->andWhere('paeg' . $i . '.gID = :group' . $i)
                ->setParameter('group' . $i, $group->getGroupID());
            $i++;
        }

        $peIDs = $query->execute()->fetchAll();
        $peID = 0;

        // Check for all the groups belonging to this AccessEntity.
        if (!empty($peIDs)) {
            foreach ($peIDs as $result) {
                $allGroups = $database->fetchColumn(
                    'select count(gID) from PermissionAccessEntityGroups
                              where peID = ' . $result['peID']
                );
                if ($allGroups == count($groups)) {
                    $peID = $result['peID'];
                    break;
                }
            }
        }
        // If the accessEntity doesnt exist, then create a new one.
        if (empty($peID)) {
            $database->insert('PermissionAccessEntities', ['petID' => $petID]);
            $peID = $database->lastInsertId();
            $app->make('config')->save(
                'concrete.misc.access_entity_updated',
                time()
            );
            foreach ($groups as $group) {
                $database->insert(
                    'PermissionAccessEntityGroups',
                    ['peID' => $peID, 'gID' => $group->getGroupID()]
                );
            }
        }

        return self::getByID($peID);
    }

    /**
     * Get the users who have access to this GroupCombination.
     *
     * @param PermissionAccess $pa
     *
     * @return array
     */
    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $userList = new UserList();
        $userList->ignorePermissions();
        foreach ($this->groups as $g) {
            $userList->filterByGroupID($g->getGroupID());
        }

        return $userList->getResults();
    }

    /**
     * Function used to load the properties for this
     * GroupCombinationEntity from the database.
     *
     * @return void
     */
    public function load()
    {
        $app = Facade::getFacadeApplication();
        /** @var $database \Concrete\Core\Database\Connection\Connection */
        $database = $app->make('database')->connection();
        $gIDs = $database->fetchAll(
            'select gID from PermissionAccessEntityGroups
            where peID = ? order by gID asc',
            [$this->peID]
        );
        if ($gIDs && is_array($gIDs)) {
            for ($i = 0; $i < count($gIDs); ++$i) {
                $g = Group::getByID($gIDs[$i]['gID']);
                if (is_object($g)) {
                    $this->groups[] = $g;
                    $this->label .= $g->getGroupDisplayName();
                    if ($i + 1 < count($gIDs)) {
                        $this->label .= t(
                            /*i18n: used for combining Group Display Names,
                             eg GroupName1 + GroupName2 */
                            ' + '
                        );
                    }
                }
            }
        }
    }
}

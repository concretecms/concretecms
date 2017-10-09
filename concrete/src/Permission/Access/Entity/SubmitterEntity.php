<?php
/**
 * @Author Ben Ali Faker
 * @Engineer/ProjectManager
 * @Company Xanweb
 * Date: 04/10/17.
 */

namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\BasicWorkflowAccess;
use Concrete\Core\Workflow\Progress\Progress;
use Database;
use Config;
use UserInfo;

class SubmitterEntity extends Entity
{
    public static function getAccessEntitiesForUser($user)
    {
        $entities = array();
        if ($user->isRegistered()) {
            $db = Database::connection();
            $pae = static::getOrCreate();
            $r = $db->fetchColumn('select cID from Pages where uID = ?', array($user->getUserID()));
            if ($r > 0) {
                $entities[] = $pae;
            }
        }

        return $entities;
    }

    public static function getOrCreate()
    {
        $db = Database::connection();
        $petID = $db->fetchColumn('select petID from PermissionAccessEntityTypes where petHandle = \'submitter\'');
        $peID = $db->fetchColumn('select peID from PermissionAccessEntities where petID = ?', array($petID));
        if (!$peID) {
            $db->executeQuery("insert into PermissionAccessEntities (petID) values(?)", array($petID));
            $peID = $db->lastInsertId();
            Config::save('concrete.misc.access_entity_updated', time());
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
    }

    public function validate(PermissionAccess $pae)
    {
        $users = $this->getAccessEntityUsers($pae);
        if (count($users) == 0) {
            return false;
        } else {
            if (is_object($users[0])) {
                $u = new \User();

                return $users[0]->getUserID() == $u->getUserID();
            }
        }
    }

    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $users=array();
        if ($pa instanceof BasicWorkflowAccess) {
            $bwp= $pa->getWorkflowProgressObject();
            if (is_object($bwp) && ($bwp instanceof Progress)) {
                $users = array($bwp->getWorkflowRequestObject()->getRequesterUserObject());
            }
        }
        return $users;
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="javascript:void(0)" onclick="ccm_choosePermissionAccessEntitySubmitter()">' . tc(
            'PermissionAccessEntityTypeName',
                'Submitter'
        ) . '</a>';

        return $html;
    }

    public function load()
    {
        $this->label = t('Submitter');
    }
}

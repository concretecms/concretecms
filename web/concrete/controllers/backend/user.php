<?php
namespace Concrete\Controller\Backend;

use Controller;
use Group;
use \Concrete\Core\User\EditResponse as UserEditResponse;
use Permissions;
use UserInfo;
use Loader;
use stdClass;
use Core;
use Exception;

class User extends Controller
{
    public function addGroup()
    {
        $this->modifyGroup('add');
    }

    public function removeGroup()
    {
        $this->modifyGroup('remove');
    }

    protected function modifyGroup($task)
    {
        $g = Group::getByID(Loader::helper('security')->sanitizeInt($_POST['gID']));
        if (is_object($g)) {
            $gp = new Permissions($g);
            if ($gp->canAssignGroup()) {
                $users = $this->getRequestUsers();
                $r = new UserEditResponse();
                $r->setUsers($users);
                $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
                foreach ($users as $ui) {
                    $uo = $ui->getUserObject();
                    if ($task == 'add') {
                        if (!$uo->inGroup($g)) {
                            $uo->enterGroup($g);
                            $obj = new stdClass();
                            $obj->gDisplayName = $g->getGroupDisplayName();
                            $obj->gID = $g->getGroupID();
                            $obj->gDateTimeEntered = $dh->formatDateTime($g->getGroupDateTimeEntered($uo));
                            $r->setAdditionalDataAttribute('groups', array($obj));
                        }
                    } else {
                        if ($uo->inGroup($g)) {
                            $uo->exitGroup($g);
                            $obj = new stdClass();
                            $obj->gID = $g->getGroupID();
                            $r->setAdditionalDataAttribute('group', $obj);
                        }
                    }
                }
                $r->outputJSON();
            } else {
                throw new Exception(t('Access Denied.'));
            }
        } else {
            throw new Exception(t('Invalid group.'));
        }

    }

    protected function getRequestUsers($permission = 'canViewUser')
    {
        $users = array();
        if (is_array($_REQUEST['uID'])) {
            $userIDs = $_REQUEST['uID'];
        } else {
            $userIDs[] = $_REQUEST['uID'];
        }
        foreach ($userIDs as $uID) {
            $ui = UserInfo::getByID($uID);
            $uip = new Permissions($ui);
            if ($uip->$permission()) {
                $users[] = $ui;
            }
        }

        if (count($users) == 0) {
            throw new Exception(t("Access Denied."));
        }

        return $users;
    }

    public function getJSON()
    {
        $users = $this->getRequestUsers();
        $r = new UserEditResponse();
        $r->setUsers($users);
        $r->outputJSON();
    }

}

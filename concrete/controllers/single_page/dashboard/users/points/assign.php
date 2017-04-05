<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users\Points;

use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Concrete\Core\User\Point\Entry as UserPointEntry;
use Concrete\Core\User\Point\Action\Action as UserPointAction;
use Concrete\Core\User\Point\Action\ActionList as UserPointActionList;
use Concrete\Core\User\Point\Action\ActionDescription as UserPointActionDescription;
use UserInfo;

class Assign extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui', 'concrete/urls', 'image', 'concrete/asset_library', 'form/user_selector', 'form/date_time');
    protected $upe;

    public function on_start()
    {
        parent::on_start();
        $html = Loader::helper('html');
        $this->upe = new UserPointEntry();
    }

    public function view($upID = null)
    {
        if (isset($upID) && $upID > 0) {
            $this->upe->load($upID);
            $this->setAttribs($this->upe);

            $u = $this->upe->getUserPointEntryUserObject();
            if (is_object($u) && $u->getUserID() > 0) {
                $this->set('upUser', $u->getUserName());
            }
        }

        $this->set('userPointActions', $this->getUserPointActions());
    }

    protected function setAttribs($upe)
    {
        $attribs = $upe->getAttributeNames();
        foreach ($attribs as $key) {
            $this->set($key, $upe->$key);
        }
    }

    public function save()
    {
        if (!\Core::make('helper/validation/token')->validate('add_community_points')) {
            $this->error = \Core::make('error');
            $this->error->add('Invalid Token');
            $this->view();

            return;
        }

        $user = $this->post('upUser');
        if (is_numeric($user)) {
            // rolling as user id
            $ui = UserInfo::getByID($user);
        } else {
            $ui = UserInfo::getByUserName($user);
            // look up userID
        }

        if (!is_object($ui)) {
            $this->error->add(t('User Required'));
        }
        if (!$this->post('upaID')) {
            $this->error->add(t('Action Required'));
        }
        if (!is_numeric($this->post('upPoints'))) {
            $this->error->add(t('Points Required'));
        }

        $action = UserPointAction::getByID($this->post('upaID'));
        if (!$action) {
            $this->error->add(t('Invalid Action'));
        }

        if (!$this->error->has()) {
            $obj = new UserPointActionDescription();
            $obj->setComments($this->post('upComments'));
            if ($this->post('manual_datetime') > 0) {
                $dt = Loader::helper('form/date_time');
                $entry = $action->addEntry($ui, $obj, $this->post('upPoints'), $dt->translate('dtoverride'));
            } else {
                $entry = $action->addEntry($ui, $obj, $this->post('upPoints'));
            }
            $this->redirect('/dashboard/users/points/assign', 'entry_saved');
        } else {
            $this->view();
        }
    }

    public function getUserPointActions()
    {
        $res = array(0 => t('-- None --'));
        $upal = new UserPointActionList();
        $upal->filterByIsActive(1);
        $userPointActions = $upal->get(0);
        if (is_array($userPointActions) && count($userPointActions)) {
            foreach ($userPointActions as $upa) {
                $res[$upa['upaID']] = h($upa['upaDefaultPoints']." - ".t($upa['upaName']));
            }
        }

        return $res;
    }

    public function getJsonActionSelectOptions()
    {
        $actions = $this->getUserPointActions();
        $res = array();
        foreach ($actions as $key => $value) {
            $res[] = array('optionValue' => $key, 'optionDisplay' => $value);
        }
        echo json_encode($res);
        exit;
    }

    public function getJsonDefaultPointAction($upaID)
    {
        $upa = new UserPointAction();
        $upa->load($upaID);
        echo json_encode($upa->getUserPointActionDefaultPoints());
        exit;
    }

    public function entry_saved()
    {
        $this->set('message', t('User Point Entry Saved'));
        $this->view();
    }
}

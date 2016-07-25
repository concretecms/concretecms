<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users\Points;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Point\Action\Action as UserPointAction;
use Concrete\Core\User\Point\Action\ActionList as UserPointActionList;
use Group;

class Actions extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui', 'concrete/urls', 'image', 'concrete/asset_library');
    public $upa;

    public function on_start()
    {
        $this->upa = new UserPointAction();
    }

    public function add()
    {
        $this->set('add_edit', t('Add'));
        $this->set('showForm', true);
        $this->view();
    }

    public function view($upaID = null)
    {
        if (isset($upaID)) {
            $this->set('add_edit', t('Edit'));
            $this->upa->load($upaID);
            $this->setAttribs($this->upa);
            $this->set('upaHasCustomClass', $this->upa->hasCustomClass());
            $g = $this->upa->getUserPointActionBadgeGroupObject();
            if (is_object($g)) {
                $this->set('upaBadgeGroupName', $g->getGroupDisplayName(false));
            }
            $this->set('showForm', true);
        }

        $actionList = $this->getActionList();
        $badges = Group::getBadges();
        $select = array('' => t('** None'));
        foreach ($badges as $g) {
            $select[$g->getGroupID()] = $g->getGroupDisplayName(false);
        }
        $this->set('badges', $select);
        $this->set('pagination', $actionList->getPagination());
        $this->set('actionList', $actionList);
        $this->set('actions', $actionList->get());
    }

    public function getActionList()
    {
        $al = new UserPointActionList();

        switch ($_REQUEST['ccm_order_by']) {
            case 'groupName':
                $al->sortBy('Groups.groupName', $_REQUEST['ccm_order_dir']);
            break;
            case 'upaDefaultPoints':
                $al->sortBy('upaDefaultPoints', $_REQUEST['ccm_order_dir']);
            break;
            case 'upaHandle':
                $al->sortBy('upaHandle', $_REQUEST['ccm_order_dir']);
            break;
            case 'upaName':
                $al->sortBy('upaName', $_REQUEST['ccm_order_dir']);
            break;
            case 'upaTypeID':
                $al->sortBy('upaTypeID', $_REQUEST['ccm_order_dir']);
            break;
            default:
                $al->sortBy('upaName', 'ASC');
            break;
        }

        return $al;
    }

    protected function setAttribs($upa)
    {
        $attribs = $upa->getAttributeNames();
        foreach ($attribs as $key) {
            $this->set($key, $upa->$key);
        }
    }

    public function save()
    {
        if (!\Core::make('helper/validation/token')->validate('add_action')) {
            $this->error = \Core::make('error');
            $this->error->add('Invalid Token');
            $this->add();

            return;
        }

        if ($this->post('upaID') > 0) {
            $this->upa->load($this->post('upaID'));
            if (!$this->upa->hasCustomClass()) {
                $this->upa->upaHandle = $this->post('upaHandle');
            }
            $this->upa->upaName = $this->post('upaName');
            $this->upa->upaDefaultPoints = $this->post('upaDefaultPoints');
            $this->upa->gBadgeID = $this->post('gBadgeID');
            if (!$this->upa->pkgID) {
                // i hate this activerecord crap
                $this->upa->pkgID = 0;
            }
            $this->upa->upaIsActive = 0;
            if ($this->post('upaIsActive')) {
                $this->upa->upaIsActive = 1;
            }

            $this->upa->save();
        } else {
            $upa = UserPointAction::add($this->post('upaHandle'), $this->post('upaName'), $this->post('upaDefaultPoints'), $this->post('gBadgeID'), $this->post('upaIsActive'));
        }

        $this->redirect('/dashboard/users/points/actions', 'action_saved');
    }

    public function delete($upaID)
    {
        if (!\Core::make('helper/validation/token')->validate('delete_action')) {
            $this->error = \Core::make('error');
            $this->error->add('Invalid Token');
            $this->view();

            return;
        }
        $this->upa->load($upaID);
        $this->upa->delete();
        $this->redirect('/dashboard/users/points/actions', 'action_deleted');
    }

    public function action_deleted()
    {
        $this->set('message', t('Community Point Action Deleted'));
        $this->view();
    }

    public function action_saved()
    {
        $this->set('message', t('Community Point Action Saved'));
        $this->view();
    }
}

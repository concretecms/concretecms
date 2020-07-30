<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users\Points;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Point\Action\Action as UserPointAction;
use Concrete\Core\User\Point\Action\ActionDescription as UserPointActionDescription;
use Concrete\Core\User\Point\Action\ActionList as UserPointActionList;
use Concrete\Core\User\Point\Entry as UserPointEntry;
use Concrete\Core\User\UserInfoRepository;

class Assign extends DashboardPageController
{
    public $helpers = ['form', 'concrete/ui', 'concrete/urls', 'image', 'concrete/asset_library', 'form/user_selector', 'form/date_time'];

    /**
     * @var UserPointEntry
     */
    protected $upe;

    public function on_start()
    {
        parent::on_start();

        $this->upe = new UserPointEntry();
    }

    public function view($upID = null)
    {
        if (isset($upID) && $upID > 0) {
            $this->upe->load($upID);

            $u = $this->upe->getUserPointEntryUserObject();
            if (is_object($u) && $u->getUserID() > 0) {
                $this->set('upUser', $u->getUserName());
            }
        }

        $this->set('userPointActions', $this->getUserPointActions());
    }

    public function save()
    {
        if (!$this->token->validate('add_community_points')) {
            $this->error->add('Invalid Token');

            return $this->view();
        }

        $post = $this->request->request;
        $user = $post->get('upUser');
        if (is_numeric($user)) {
            // rolling as user id
            $ui = $this->app->make(UserInfoRepository::class)->getByID($user);
        } else {
            $ui = $this->app->make(UserInfoRepository::class)->getByName($user);
            // look up userID
        }

        if (!is_object($ui)) {
            $this->error->add(t('User Required'));
        }

        if (!($upaID = $post->get('upaID'))) {
            $this->error->add(t('Action Required'));
        }

        if (!is_numeric($upPoints = $post->get('upPoints'))) {
            $this->error->add(t('Points Required'));
        }

        $action = UserPointAction::getByID($upaID);
        if (!$action) {
            $this->error->add(t('Invalid Action'));
        }

        if (!$this->error->has()) {
            $obj = new UserPointActionDescription();
            $obj->setComments($post->get('upComments'));
            if ($this->post('manual_datetime') > 0) {
                $dt = $this->app->make('helper/form/date_time');
                $entry = $action->addEntry($ui, $obj, $upPoints, $dt->translate('dtoverride'));
            } else {
                $entry = $action->addEntry($ui, $obj, $upPoints);
            }

            return $this->buildRedirect($this->action('entry_saved'));
        }

        $this->view();
    }

    public function getUserPointActions()
    {
        $res = [0 => t('-- None --')];
        $upal = new UserPointActionList();
        $upal->filterByIsActive(1);
        $userPointActions = $upal->get(0);
        if (is_array($userPointActions) && count($userPointActions)) {
            foreach ($userPointActions as $upa) {
                $res[$upa['upaID']] = h($upa['upaDefaultPoints'] . ' - ' . t($upa['upaName']));
            }
        }

        return $res;
    }

    public function getJsonActionSelectOptions()
    {
        $actions = $this->getUserPointActions();
        $res = [];
        foreach ($actions as $key => $value) {
            $res[] = ['optionValue' => $key, 'optionDisplay' => $value];
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($res);
    }

    public function getJsonDefaultPointAction($upaID)
    {
        $upa = new UserPointAction();
        $upa->load($upaID);

        return $this->app->make(ResponseFactoryInterface::class)->json($upa->getUserPointActionDefaultPoints());
    }

    public function entry_saved()
    {
        $this->set('message', t('User Point Entry Saved'));
        $this->view();
    }
}

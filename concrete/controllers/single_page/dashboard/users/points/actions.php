<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users\Points;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Point\Action\Action as UserPointAction;
use Concrete\Core\User\Point\Action\ActionList as UserPointActionList;

class Actions extends DashboardPageController
{
    public $helpers = ['form', 'concrete/ui', 'concrete/urls', 'image', 'concrete/asset_library'];

    /**
     * @var UserPointAction
     */
    public $upa;

    public function on_start()
    {
        parent::on_start();

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
        $select = ['' => t('** None')];
        foreach ($badges as $g) {
            $select[$g->getGroupID()] = $g->getGroupDisplayName(false);
        }

        $factory = $this->app->make(PaginationFactory::class, [$this->request]);
        $pagination = $factory->createPaginationObject($actionList);

        $this->set('badges', $select);
        $this->set('pagination', $pagination);
        $this->set('actionList', $actionList);
        $this->set('actions', $pagination->getCurrentPageResults());
    }

    public function getActionList(): UserPointActionList
    {
        $al = new UserPointActionList();

        if (!$this->request->query->has('ccm_order_by')) {
            $al->sortBy('upa.upaName', 'ASC');
        }

        return $al;
    }

    public function save()
    {
        if (!$this->token->validate('add_action')) {
            $this->error->add('Invalid Token');

            return $this->add();
        }

        $post = $this->request->request;
        if (($upaID = $post->get('upaID')) > 0) {
            $this->upa->load($upaID);
            if (!$this->upa->hasCustomClass()) {
                $this->upa->upaHandle = $post->get('upaHandle');
            }

            $this->upa->upaName = $post->get('upaName');
            $this->upa->upaDefaultPoints = (int) $post->get('upaDefaultPoints');
            $this->upa->gBadgeID = (int) ($post->get('gBadgeID') ?? 0);
            $this->upa->upaIsActive = (int) ($post->get('upaIsActive') ?? 0);

            if (!$this->upa->pkgID) {
                // i hate this activerecord crap
                $this->upa->pkgID = 0;
            }

            $this->upa->save();
        } else {
            UserPointAction::add($post->get('upaHandle'), $post->get('upaName'), $post->get('upaDefaultPoints'), $post->get('gBadgeID'), $post->get('upaIsActive'));
        }

        return $this->buildRedirect($this->action('action_saved'));
    }

    public function delete($upaID)
    {
        if (!$this->token->validate('delete_action')) {
            $this->error->add('Invalid Token');

            return $this->view();
        }

        $this->upa->load($upaID);
        $this->upa->delete();

        return $this->buildRedirect($this->action('action_deleted'));
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

    protected function setAttribs(UserPointAction $upa)
    {
        $attribs = $upa->getAttributeNames();
        foreach ($attribs as $key) {
            $this->set($key, $upa->{$key});
        }
    }
}

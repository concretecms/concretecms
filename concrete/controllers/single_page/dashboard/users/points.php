<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\User\Point\Entry as UserPointEntry;
use Concrete\Core\User\Point\EntryList as UserPointEntryList;

class Points extends DashboardPageController
{
    public $helpers = ['form', 'concrete/ui', 'concrete/urls', 'image', 'concrete/asset_library', 'form/user_selector'];

    public function view()
    {
        $upEntryList = $this->getEntries();
        $factory = $this->app->make(PaginationFactory::class, [$this->request]);
        $pagination = $factory->createPaginationObject($upEntryList);

        $this->set('pagination', $pagination);
        $this->set('upEntryList', $upEntryList);
        $this->set('entries', $pagination->getCurrentPageResults());
        $this->set('dh', $this->app->make('date'));
    }

    public function getEntries(): UserPointEntryList
    {
        $entries = new UserPointEntryList();
        $entries->setItemsPerPage(100);

        if ($uID = $this->request->get('uID')) {
            $entries->filterByUserID($uID);
        }

        if ($uName = $this->request->get('uName')) {
            $entries->filterByUserName($uName);
        }

        $upaName = (string) $this->request->get('upaName', '');
        if ($upaName !== '') {
            $entries->filterByUserPointActionName($upaName);
        }

        if (!$this->request->query->has('ccm_order_by')) {
            $entries->sortBy('uph.timestamp', 'desc');
        }

        return $entries;
    }

    public function deleteEntry($upID)
    {
        if (!$this->token->validate('delete_community_points')) {
            $this->error->add('Invalid Token');

            return $this->view();
        }

        $up = new UserPointEntry();
        $up->load($upID);
        $up->delete();

        return $this->buildRedirect($this->action('entry_deleted'));
    }

    public function entry_deleted()
    {
        $this->set('message', t('User Point Entry Deleted'));
        $this->view();
    }
}

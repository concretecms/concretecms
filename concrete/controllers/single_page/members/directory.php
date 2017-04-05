<?php
namespace Concrete\Controller\SinglePage\Members;

use Concrete\Core\Page\Controller\PublicProfilePageController;
use UserAttributeKey;
use Loader;
use Concrete\Core\User\UserList;

class Directory extends PublicProfilePageController
{
    /**
     * @var \Concrete\Core\User\UserList
     */
    protected $userList;

    public function on_start()
    {
        parent::on_start();
        $this->requireAsset('css', 'core/frontend/pagination');
        $this->userList = new UserList();
        $this->userList->sortByUserID();
    }

    public function on_before_render()
    {
        $pagination = $this->userList->getPagination();
        $users = $pagination->getCurrentPageResults();
        $this->set('userList', $this->userList);
        $this->set('users', $users);
        $this->set('total', $pagination->getTotalResults());
        $this->set('attribs', UserAttributeKey::getMemberListList());
        $this->set('keywords', isset($_REQUEST['keywords']) ? $this->app->make('helper/text')->entities($_REQUEST['keywords']) : '');
        $this->set('pagination', $pagination);
    }

    public function search_members()
    {
        $keywords = $this->get('keywords');
        if ($keywords != '') {
            $this->userList->filterByKeywords($keywords);
        }
    }
}

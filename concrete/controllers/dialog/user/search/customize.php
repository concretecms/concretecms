<?php
namespace Concrete\Controller\Dialog\User\Search;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\User\Search\SearchProvider;
use User;
use UserAttributeKey;
use Loader;
use UserList;
use URL;

class Customize extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/search/customize';

    protected function canAccess()
    {
        $sh = Loader::helper('concrete/user');

        return $sh->canAccessUserSearchInterface();
    }

    public function view()
    {
        /**
         * @var $provider SearchProvider
         */
        $provider = $this->app->make('Concrete\Core\User\Search\SearchProvider');
        $element = new CustomizeResults($provider);
        $this->set('customizeElement', $element);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $u = new User();
            $fdc = new \Concrete\Core\User\Search\ColumnSet\ColumnSet();
            $fldca = new \Concrete\Core\User\Search\ColumnSet\Available();
            foreach ($_POST['column'] as $key) {
                $fdc->addColumn($fldca->getColumnByKey($key));
            }
            $sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
            $fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
            $u->saveConfig('USER_LIST_DEFAULT_COLUMNS', serialize($fdc));

            $userList = new UserList();
            $columns = \Concrete\Core\User\Search\ColumnSet\ColumnSet::getCurrent();
            $col = $columns->getDefaultSortColumn();
            $userList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());

            $ilr = new \Concrete\Core\User\Search\Result\Result($columns, $userList, URL::to('/ccm/system/search/users/submit'));
            $r = new \Concrete\Core\Search\Response();
            $r->setMessage(t('User search columns saved successfully.'));
            $r->setSearchResult($ilr);
            $r->outputJSON();
        }
    }
}

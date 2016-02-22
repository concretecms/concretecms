<?php
namespace Concrete\Controller\Dialog\File\Search;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\File\Search\ColumnSet\ColumnSet as FileSearchColumnSet;
use Concrete\Core\File\Search\ColumnSet\Available as FileSearchAvailableColumnSet;
use Concrete\Core\File\Search\SearchProvider;
use FileAttributeKey;
use Concrete\Core\File\Search\Result\Result as FileSearchResult;
use Concrete\Core\Search\Response as SearchResponse;
use Loader;
use User;
use FileList;
use URL;

class Customize extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/search/customize';

    protected function canAccess()
    {
        $sh = Loader::helper('concrete/dashboard/sitemap');

        return $sh->canRead();
    }

    public function view()
    {
        /**
         * @var $provider SearchProvider
         */
        $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
        $element = new CustomizeResults($provider);
        $this->set('customizeElement', $element);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $u = new User();
            $fdc = new FileSearchColumnSet();
            $fldca = new FileSearchAvailableColumnSet();
            foreach ($_POST['column'] as $key) {
                $fdc->addColumn($fldca->getColumnByKey($key));
            }
            $sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
            $fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
            $u->saveConfig('FILE_LIST_DEFAULT_COLUMNS', serialize($fdc));

            $fileList = new FileList();
            $columns = FileSearchColumnSet::getCurrent();
            $col = $columns->getDefaultSortColumn();
            $fileList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());

            $ilr = new FileSearchResult($columns, $fileList, URL::to('/ccm/system/search/files/submit'));
            $r = new SearchResponse();
            $r->setMessage(t('File search columns saved successfully.'));
            $r->setSearchResult($ilr);
            $r->outputJSON();
        }
    }
}

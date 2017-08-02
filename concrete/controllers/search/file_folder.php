<?php
namespace Concrete\Controller\Search;


use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileFolder extends AbstractController
{
    protected $list;
    protected $result;
    protected $filesystem;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    public function search(Query $query = null)
    {
        $searchRequest = new StickyRequest('file_manager_folder');

        if ($this->request->get('folder')) {
            $searchRequest->resetSearchRequest();
            $node = Node::getByID($this->request->get('folder'));
            if (is_object($node) &&
                ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder ||
                    $node instanceof SearchPreset)) {
                    $folder = $node;
            }
        } else {
            $req = $searchRequest->getSearchRequest();
            if (isset($req['folder'])) {
                $node = Node::getByID($req['folder']);
                if (is_object($node) &&
                    ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder ||
                        $node instanceof SearchPreset)) {
                    $folder = $node;
                }
            }
        }

        if (isset($folder)) {

            if ($folder instanceof SearchPreset) {
                $search = $folder->getSavedSearchObject();
                $query = $search->getQuery();
                $provider = \Core::make('Concrete\Core\File\Search\SearchProvider');
                $ilr = $provider->getSearchResultFromQuery($query);
                $ilr->setBaseURL(\URL::to('/ccm/system/search/files/preset', $search->getID()));
            }

            $searchRequest->addToSearchRequest('folder', $folder->getTreeNodeID());

        }

        if (!isset($ilr)) {

            if (!isset($folder)) {
                $folder = $this->filesystem->getRootFolder();
            }
            $u = new \User();
            $list = $folder->getFolderItemList($u, $this->request);
            $fields = $this->request->get('field');
            $filters = array();
            if (count($fields)) { // We are passing in something like "filter by images"
                $manager = ManagerFactory::get('file_folder');
                $filters = $manager->getFieldsFromRequest($this->request->query->all());
            }

            if (count($filters)) {
                /**
                 * @var $field FieldInterface
                 */
                foreach($filters as $field) {
                    $field->filterList($list);
                }
            }

            $columns = new FolderSet();

            $list->disableAutomaticSorting(); // We don't need the automatic sorting found in the item list. it fires too late.
            $data = $this->request->query->all();

            if (!$list->getActiveSortColumn()) {
                $column = $columns->getDefaultSortColumn();
                $list->sortBySearchColumn($column);
            }

            if (isset($data[$list->getQuerySortColumnParameter()])) {
                $value = $data[$list->getQuerySortColumnParameter()];
                $sortColumn = $columns->getColumnByKey($value);

                if (isset($data[$list->getQuerySortDirectionParameter()])) {
                    $direction = $data[$list->getQuerySortDirectionParameter()];
                } else{
                    $direction = $sortColumn->getColumnDefaultSortDirection();
                }

                if ($sortColumn) {
                    $sortColumn->setColumnSortDirection($direction);
                    $list->sortBySearchColumn($sortColumn, $direction);
                }
            }

            if ($list instanceof PagerProviderInterface) {
                $manager = $list->getPagerManager();
                $manager->sortListByCursor($list, $list->getActiveSortDirection());
            }

            $ilr = new Result($columns, $list, \URL::to('/ccm/system/file/folder/contents'));
            if ($filters) {
                $ilr->setFilters($filters);
            }

        }

        $breadcrumb = [];
        if ($folder->getTreeNodeParentID() > 0) {
            $nodes = array_reverse($folder->getTreeNodeParentArray());
            $ilr->setFolder($folder);

            foreach($nodes as $node) {
                $breadcrumb[] = [
                    'active' => false,
                    'name' => $node->getTreeNodeDisplayName(),
                    'folder' => $node->getTreeNodeID(),
                    'url' => (string) \URL::to('/ccm/system/file/folder/contents'),
                    'menu' => $node->getTreeNodeMenu()
                ];
            }

            $breadcrumb[] = [
                'active' => true,
                'name' => $folder->getTreeNodeDisplayName(),
                'folder' => $folder->getTreeNodeID(),
                'menu' => $folder->getTreeNodeMenu(),
                'url' => (string) \URL::to('/ccm/system/file/folder/contents')
            ];

        }

        $ilr->setBreadcrumb($breadcrumb);
        $this->result = $ilr;
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    protected function canAccess()
    {
        $fp = \FilePermissions::getGlobal();
        return $fp->canAccessFileManager();
    }

    public function submit()
    {
        if ($this->canAccess()) {
            $this->search();
            return new JsonResponse($this->result->getJSONObject());
        }
        $this->app->shutdown();
    }
}

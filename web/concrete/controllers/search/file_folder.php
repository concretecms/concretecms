<?php
namespace Concrete\Controller\Search;


use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileFolder extends AbstractController
{
    protected $list;
    protected $result;
    protected $searchRequest;
    protected $filesystem;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    public function search()
    {
        if ($this->request->get('folder')) {
            $node = Node::getByID($this->request->get('folder'));
            if (is_object($node) &&
                ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder ||
                    $node instanceof SearchPreset)) {
                    $folder = $node;
            }
        }

        if (isset($folder) && $folder instanceof SearchPreset) {

            $this->list = new FileList();
            $search = $folder->getSavedSearchObject();
            $query = $search->getQuery();
            $provider = \Core::make('Concrete\Core\File\Search\SearchProvider');
            $ilr = $provider->getSearchResultFromQuery($query);
            $ilr->setBaseURL(\URL::to('/ccm/system/search/files/preset', $search->getID()));

        }

        if (!isset($ilr)) {

            $this->list = new FolderItemList($this->searchRequest);

            if (!isset($folder)) {
                $folder = $this->filesystem->getRootFolder();
            }

            $this->list->filterByParentFolder($folder);
            $this->list->sortByNodeName();
            $columns = new FolderSet();
            $ilr = new Result($columns, $this->list, \URL::to('/ccm/system/file/folder/contents'));
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
                    'url' => (string) \URL::to('/ccm/system/file/folder/contents')
                ];
            }

            $breadcrumb[] = [
                'active' => true,
                'name' => $folder->getTreeNodeDisplayName(),
                'folder' => $folder->getTreeNodeID(),
                'url' => (string) \URL::to('/ccm/system/file/folder/contents'),
            ];

        }


        $ilr->setBreadcrumb($breadcrumb);

        $this->result = $ilr;
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->list;
    }

    public function submit()
    {
        $this->search();
        return new JsonResponse($this->result->getJSONObject());
    }
}

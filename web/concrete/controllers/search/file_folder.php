<?php
namespace Concrete\Controller\Search;


use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
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
        $this->searchRequest = new StickyRequest('file_manager');
        $this->list = new FolderItemList($this->searchRequest);
    }

    public function search()
    {
        $this->list->sortByNodeName();
        $searchRequest = $this->searchRequest->getSearchRequest();
        if (isset($searchRequest['folder'])) {
            $node = Node::getByID($searchRequest['folder']);
            if (is_object($node) && $node instanceof \Concrete\Core\Tree\Node\Type\FileFolder) {
                $folder = $node;
            }
        }

        if (!isset($folder)) {
            $folder = $this->filesystem->getRootFolder();
        }

        $this->list->filterByParentFolder($folder);

        $columns = new FolderSet();
        $ilr = new Result($columns, $this->list, \URL::to('/ccm/system/file/folder/contents'));

        $breadcrumb = [];
        if ($folder->getTreeNodeParentID() > 0) {
            $nodes = array_reverse($folder->getTreeNodeParentArray());
            $ilr->setFolder($folder);

            foreach($nodes as $node) {
                $breadcrumb[] = [
                    'active' => false,
                    'name' => $node->getTreeNodeDisplayName(),
                    'folder' => $node->getTreeNodeID(),
                    'url' => (string) $ilr->getBaseURL()
                ];
            }

            $breadcrumb[] = [
                'active' => true,
                'name' => $folder->getTreeNodeDisplayName(),
                'folder' => $folder->getTreeNodeID(),
                'url' => (string) $ilr->getBaseURL(),
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

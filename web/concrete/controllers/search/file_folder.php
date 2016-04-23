<?php
namespace Concrete\Controller\Search;


use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
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
        $folder = $this->filesystem->getRootFolder();
        $this->list = new FolderItemList($folder);
    }

    public function search()
    {
        $this->list->sortByNodeName();
        $columns = new FolderSet();
        $ilr = new Result($columns, $this->list, \URL::to('/ccm/system/file/folder/contents'));
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

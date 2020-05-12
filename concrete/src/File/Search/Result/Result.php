<?php
namespace Concrete\Core\File\Search\Result;

use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{

    protected $folder;

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getJSONObject()
    {
        $r = parent::getJSONObject();
        $r->folder = $this->folder;
        return $r;
    }

    public function getItemDetails($item)
    {
        $node = new Item($this, $this->listColumns, $item);

        return $node;
    }

    public function getColumnDetails($column)
    {
        $node = new Column($this, $column);

        return $node;
    }
}

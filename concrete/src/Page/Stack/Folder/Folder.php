<?php
namespace Concrete\Core\Page\Stack\Folder;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\StackFolder;
use Concrete\Core\Page\Page;

class Folder implements ExportableInterface
{

    protected $connection;
    protected $page;

    public function __construct(Page $page, Connection $connection)
    {
        $this->connection = $connection;
        $this->page = $page;
    }

    public function getExporter()
    {
        return new StackFolder();
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }


}
<?php
namespace Concrete\Core\Page\Stack\Folder;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;

class Folder
{

    protected $connection;
    protected $page;

    public function __construct(Page $page, Connection $connection)
    {
        $this->connection = $connection;
        $this->page = $page;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }


}
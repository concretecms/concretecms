<?php
namespace Concrete\Core\Page\Stack\Folder;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Type\Type;

class FolderService
{

    protected $connection;
    protected $application;

    public function __construct(Application $application, Connection $connection)
    {
        $this->connection = $connection;
        $this->application = $application;
    }

    public function add($name, Folder $folder = null)
    {
        $type = Type::getByHandle(STACK_CATEGORY_PAGE_TYPE);
        $parent = $folder ? $folder->getPageObject() : \Page::getByPath(STACKS_PAGE_PATH);
        $data = array();
        $data['name'] = $name;
        $page = $parent->add($type, $data);

        return $this->application->make('Concrete\Core\Page\Stack\Folder\Folder', array($page));

    }

}
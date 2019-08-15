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

    public function getByPath($path)
    {
        $c = \Concrete\Core\Page\Page::getByPath(STACKS_PAGE_PATH . '/' . trim($path, '/'));
        if ($c->getCollectionTypeHandle() == STACK_CATEGORY_PAGE_TYPE) {
            return $this->application->make('Concrete\Core\Page\Stack\Folder\Folder', array($c));
        }
    }

    public function getByID($cID)
    {
        $c = \Concrete\Core\Page\Page::getByID($cID);
        if ($c->getCollectionTypeHandle() == STACK_CATEGORY_PAGE_TYPE) {
            return $this->application->make('Concrete\Core\Page\Stack\Folder\Folder', array($c));
        }
    }

    public function add($name, Folder $folder = null)
    {
//        $site = \Core::make('site')->getActiveSiteForEditing();
        $type = Type::getByHandle(STACK_CATEGORY_PAGE_TYPE);
        $parent = $folder ? $folder->getPage() : \Concrete\Core\Page\Page::getByPath(STACKS_PAGE_PATH);
        $data = array();
        $data['name'] = $name;
        $page = $parent->add($type, $data);

        return $this->application->make('Concrete\Core\Page\Stack\Folder\Folder', array($page));

    }

}
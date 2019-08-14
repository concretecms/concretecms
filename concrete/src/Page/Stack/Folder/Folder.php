<?php
namespace Concrete\Core\Page\Stack\Folder;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\StackFolder;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Key\Key;

/**
 * @since 8.0.0
 */
class Folder implements ExportableInterface, AssignableObjectInterface
{

    protected $connection;
    protected $page;

    public function __construct(Page $page, Connection $connection)
    {
        $this->connection = $connection;
        $this->page = $page;
    }

    /**
     * @since 8.2.0
     */
    public function setChildPermissionsToOverride()
    {
        $this->page->setChildPermissionsToOverride();
    }

    /**
     * @since 8.2.0
     */
    public function setPermissionsToOverride()
    {
        $this->page->setPermissionsToOverride();
    }

    /**
     * @since 8.2.0
     */
    public function assignPermissions(
        $userOrGroup,
        $permissions,
        $accessType = Key::ACCESS_TYPE_INCLUDE,
        $cascadeToChildren = true
    ) {
        $this->page->assignPermissions($userOrGroup, $permissions,$accessType, $cascadeToChildren);
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
<?php
namespace Concrete\Core\Permission\Response;

use Page;
use Permissions;

/**
 * Class CollectionVersionResponse
 * @package Concrete\Core\Permission\Response
 */
class CollectionVersionResponse extends Response
{
    /** @var \Concrete\Core\Page\Collection\Version\Version $object */
    protected $object;

    public function testForErrors()
    {
        if (!$this->object->getVersionID()) {
            $c = Page::getByID($this->object->getCollectionID());
            $cp = new Permissions($c);
            if ($cp->canViewPageVersions()) {
                return COLLECTION_FORBIDDEN;
            } else {
                return COLLECTION_NOT_FOUND;
            }
        } elseif (!$this->object->isMostRecent()) {
            return VERSION_NOT_RECENT;
        }
        return parent::testForErrors();
    }

}
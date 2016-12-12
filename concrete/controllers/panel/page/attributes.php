<?php
namespace Concrete\Controller\Panel\Page;

use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Permissions;
use Page;
use stdClass;
use PermissionKey;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

class Attributes extends BackendInterfacePageController
{

    protected $viewPath = '/panels/page/attributes';

    public function canAccess()
    {
        return $this->permissions->canEditPageProperties();
    }

    public function view()
    {
        $pk = PermissionKey::getByHandle('edit_page_properties');
        $pk->setPermissionObject($this->page);
        $assignment = $pk->getMyAssignment();
        $allowed = $assignment->getAttributesAllowedArray();

        $category = AttributeKeyCategory::getByHandle('collection');
        $sets = $category->getAttributeSets();
        $leftovers = $category->getUnassignedAttributeKeys();

        $selectedAttributes = $this->page->getSetCollectionAttributes();
        $selectedAttributeIDs = array();
        foreach ($selectedAttributes as $ak) {
            $selectedAttributeIDs[] = $ak->getAttributeKeyID();
        }

        $data = array();
        foreach ($sets as $set) {
            $obj = new stdClass();
            $obj->title = $set->getAttributeSetDisplayName();
            $obj->attributes = array();
            foreach ($set->getAttributeKeys() as $ak) {
                if (in_array($ak->getAttributeKeyID(), $allowed)) {
                    $obj->attributes[] = $ak;
                }
            }
            if (count($obj->attributes)) {
                $data[] = $obj;
            }
        }
        if (count($leftovers)) {
            $obj = new stdClass();
            $obj->title = t('Other');
            $obj->attributes = array();
            foreach ($leftovers as $ak) {
                if (in_array($ak->getAttributeKeyID(), $allowed)) {
                    $obj->attributes[] = $ak;
                }
            }
            if (count($obj->attributes)) {
                $data[] = $obj;
            }
        }

        $this->set('selectedAttributeIDs', $selectedAttributeIDs);
        $this->set('assignment', $asl);
        $this->set('attributes', $data);
    }

}

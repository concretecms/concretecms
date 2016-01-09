<?php
namespace Concrete\Core\Permission\Key;
use Loader;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use User;
class EditPagePropertiesPageKey extends PageKey  {

	protected function getAllAttributeKeyIDs() {
		$db = Loader::db();
		$allAKIDs = $db->GetCol('select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akCategoryHandle = \'collection\'');
		return $allAKIDs;
	}

	public function getMyAssignment() {
		$u = new User();
		$asl = new \Concrete\Core\Permission\Access\ListItem\EditPagePropertiesPageListItem();


		if ($u->isSuperUser()) {
			$asl->setAllowEditName(1);
			$asl->setAllowEditDateTime(1);
			$asl->setAllowEditUserID(1);
			$asl->setAllowEditDescription(1);
			$asl->setAllowEditPaths(1);
			$asl->setAttributesAllowedArray($this->getAllAttributeKeyIDs());
			$asl->setAttributesAllowedPermission('A');
			return $asl;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return $asl;
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $pae->getAccessListItems(PageKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		$properties = array();

		$excluded = array();
		$akIDs = array();
		$u = new User();
		if (count($list) > 0) {
			$allAKIDs = $this->getAllAttributeKeyIDs();
		}
		foreach($list as $l) {

			if ($l->allowEditName() && (!in_array('name', $excluded))) {
				$asl->setAllowEditName(1);
			}
			if ($l->allowEditDateTime() && (!in_array('date', $excluded))) {
				$asl->setAllowEditDateTime(1);
			}
			if ($l->allowEditUserID() && (!in_array('uID', $excluded))) {
				$asl->setAllowEditUserID(1);
			}
			if ($l->allowEditDescription() && (!in_array('description', $excluded))) {
				$asl->setAllowEditDescription(1);
			}
			if ($l->allowEditPaths() && (!in_array('paths', $excluded))) {
				$asl->setAllowEditPaths(1);
			}

			if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditName()) {
				$asl->setAllowEditName(0);
				$excluded[] = 'name';
			}
			if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditDateTime()) {
				$asl->setAllowEditDateTime(0);
				$excluded[] = 'date';
			}
			if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditUserID()) {
				$asl->setAllowEditUserID(0);
				$excluded[] = 'uID';
			}
			if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditDescription()) {
				$asl->setAllowEditDescription(0);
				$excluded[] = 'description';
			}
			if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditPaths()) {
				$asl->setAllowEditPaths(0);
				$excluded[] = 'paths';
			}

			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
				if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE) {
					$akIDs = array_values(array_diff($akIDs, $l->getAttributesAllowedArray()));
				} else {
					$akIDs = array_unique(array_merge($akIDs, $l->getAttributesAllowedArray()));
				}
			}

			if ($l->getAttributesAllowedPermission() == 'A') {
				$akIDs = $allAKIDs;
				$asl->setAttributesAllowedPermission('A');
			}
		}

		$asl->setAttributesAllowedArray($akIDs);
		return $asl;
	}


	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$asl = $this->getMyAssignment();
		if (is_object($obj)) {
			if ($obj instanceof CollectionAttributeKey) {
				if ($asl->getAttributesAllowedPermission() == 'A') {
					return true;
				}
				if ($asl->getAttributesAllowedPermission() == 'C' && in_array($obj->getAttributeKeyID(), $asl->getAttributesAllowedArray())) {
					return true;
				} else {
					return false;
				}
			}
		}

		if (
			$asl->allowEditName() ||
			$asl->allowEditDescription() ||
			$asl->allowEditDateTime() ||
			$asl->allowEditUserID() ||
			$asl->allowEditPaths() ||
			($asl->getAttributesAllowedPermission() == 'A' || ($asl->getAttributesAllowedPermission() == 'C' && count($asl->getAttributesAllowedArray() > 0)))) {
				return true;
		} else {
			return false;
		}
	}


}

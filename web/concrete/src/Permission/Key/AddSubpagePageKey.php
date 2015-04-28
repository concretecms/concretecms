<?php
namespace Concrete\Core\Permission\Key;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;
class AddSubpagePageKey extends PageKey  {

	public function canAddExternalLink() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PageKey::ACCESS_TYPE_ALL, $accessEntities);
		$canAddLinks = false;
		foreach($list as $l) {
			if (!$l->allowExternalLinks()) {
				$canAddLinks = false;
			} else {
				$canAddLinks = true;
			}
		}
		return $canAddLinks;
	}

	protected function getAllowedPageTypeIDs() {

		$u = new User();
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PageKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		$db = Loader::db();
		$ptIDs = array();
		if (count($list) > 0) {
			$allPTIDs = $db->GetCol('select ptID from PageTypes where ptIsInternal = 0');
			foreach($list as $l) {
				if ($l->getPageTypesAllowedPermission() == 'N') {
					$ptIDs = array();
				}
				if ($l->getPageTypesAllowedPermission() == 'C') {
					if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE) {
						$ptIDs = array_values(array_diff($ptIDs, $l->getPageTypesAllowedArray()));
					} else {
						$ptIDs = array_unique(array_merge($ptIDs, $l->getPageTypesAllowedArray()));
					}
				}
				if ($l->getPageTypesAllowedPermission() == 'A') {
					$ptIDs = $allPTIDs;
				}
			}
		}

		return $ptIDs;
	}

	public function validate($ct = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedPageTypeIDs();
		if ($ct != false) {
			return in_array($ct->getPageTypeID(), $types);
		} else {
			return count($types) > 0;
		}
	}



}

<?php
namespace Concrete\Core\Permission\Key;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;
class AddBlockToAreaAreaKey extends AreaKey  {

	public function copyFromPageToArea() {
		$db = Loader::db();
		$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array('add_block'));
		$r = $db->Execute('select peID, pa.paID from PermissionAssignments pa inner join PermissionAccessList pal on pa.paID = pal.paID where pkID = ?', array(
			$inheritedPKID
		));
		if ($r) {
			while ($row = $r->FetchRow()) {
				$db->Replace('AreaPermissionAssignments', array(
					'cID' => $this->permissionObject->getCollectionID(),
					'arHandle' => $this->permissionObject->getAreaHandle(),
					'pkID' => $this->getPermissionKeyID(),
					'paID' => $row['paID']
				), array('cID', 'arHandle', 'pkID'), true);

				$rx = $db->Execute('select permission from BlockTypePermissionBlockTypeAccessList where paID = ? and peID = ?', array(
						$row['paID'], $row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAccessList', array(
						'peID' => $row['peID'],
						'permission' => $rowx['permission'],
						'paID' => $row['paID']
					), array('paID', 'peID'), true);
				}
				$db->Execute('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', array(
					$row['paID']
				));
				$rx = $db->Execute('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ? and peID = ?', array(
						$row['paID'], $row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAccessListCustom', array(
						'paID' => $row['paID'],
						'btID' => $rowx['btID'],
						'peID' => $row['peID']
					), array('paID', 'peID', 'btID'), true);
				}
			}
		}
	}


	protected function getAllowedBlockTypeIDs() {

		$u = new User();
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(AreaKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		$db = Loader::db();
		$btIDs = array();
		if (count($list) > 0) {
			$dsh = Loader::helper('concrete/dashboard');
			if ($dsh->inDashboard()) {
				$allBTIDs = $db->GetCol('select btID from BlockTypes');
			} else {
				$allBTIDs = $db->GetCol('select btID from BlockTypes where btIsInternal = 0');
			}
			foreach($list as $l) {
				if ($l->getBlockTypesAllowedPermission() == 'N') {
					$btIDs = array();
				}
				if ($l->getBlockTypesAllowedPermission() == 'C') {
					if ($l->getAccessType() == AreaKey::ACCESS_TYPE_EXCLUDE) {
						$btIDs = array_values(array_diff($btIDs, $l->getBlockTypesAllowedArray()));
					} else {
						$btIDs = array_unique(array_merge($btIDs, $l->getBlockTypesAllowedArray()));
					}
				}
				if ($l->getBlockTypesAllowedPermission() == 'A') {
					$btIDs = $allBTIDs;
				}
			}
		}

		return $btIDs;
	}

	public function validate($bt = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedBlockTypeIDs();
		if ($bt != false) {
			return in_array($bt->getBlockTypeID(), $types);
		} else {
			return count($types) > 0;
		}
	}


}

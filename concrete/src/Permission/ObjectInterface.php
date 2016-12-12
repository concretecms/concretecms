<?php
namespace Concrete\Core\Permission;
interface ObjectInterface {

	public function getPermissionResponseClassName();
	public function getPermissionAssignmentClassName();
	public function getPermissionObjectKeyCategoryHandle();
	public function getPermissionObjectIdentifier();
}

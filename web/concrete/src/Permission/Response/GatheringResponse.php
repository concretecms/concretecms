<?php
namespace Concrete\Core\Permission\Response;
use PermissionKey;
class GatheringResponse extends Response {

	public function canEditGatheringItems() {
		// Eventually this will be overrideable at a particular gathering level.
		$tp = PermissionKey::getByHandle('edit_gatherings');
		return $tp->can();
	}

}

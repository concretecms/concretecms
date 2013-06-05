<?php

class Concrete5_Model_WonBadgeUserPointAction extends UserPointAction {
	

	public function addDetailedEntry($user, $group) {
		$obj = new WonBadgeUserPointActionDescription();
		$obj->setBadgeGroupID($group->getGroupID());
		$entry = self::addEntry($user, $obj, $group->getGroupBadgeCommunityPointValue());
	}

}

class Concrete5_Model_WonBadgeUserPointActionDescription extends UserPointActionDescription {

	public function setBadgeGroupID($gID) {
		$this->gID = $gID;
	}

	public function getUserPointActionDescription() {
		$group = Group::getByID($this->gID);
		return t('Won the <strong>%s</strong> Badge', $group->getGroupName());
	}

}
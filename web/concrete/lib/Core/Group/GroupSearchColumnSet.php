<?php
namespace Concrete\Core\Group;
class GroupSearchColumnSet extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	
	public function getGroupName($g) {
		return '<a data-group-name="' . $g->getGroupDisplayName() . '" href="' . URL::to('/dashboard/users/groups', 'edit', $g->getGroupID()) . '" data-group-id="' . $g->getGroupID() . '" href="#">' . $g->getGroupDisplayName() . '</a>';
	}

	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('gName', t('Name'), array('GroupSearchColumnSet', 'getGroupName')));
		$gName = $this->getColumnByKey('gName');
		$this->setDefaultSortColumn($gName, 'asc');
	}
}
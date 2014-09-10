<?php 
namespace Concrete\Core\User\Group;
use Concrete\Core\User\User;

abstract class GroupAutomationController {

	/** 
	 * Return true to automatically enter the current ux into the group
	 */
	abstract public function check(User $ux);

	public function getGroupObject() {
		return $this->group;
	}

	public function __construct(Group $g) {
		$this->group = $g;
	}

}
<?php defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_GroupAutomationController {

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
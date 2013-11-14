<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_UserSearchDefaultColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'UserAttributeKey';	
	
	public function getUserName($ui) {
		return '<a href="' . View::url('/dashboard/users/search') . '?uID=' . $ui->getUserID() . '">' . $ui->getUserName() . '</a>';
	}

	public function getUserEmail($ui) {
		return '<a href="mailto:' . $ui->getUserEmail() . '">' . $ui->getUserEmail() . '</a>';
	}
	
	public static function getUserDateAdded($ui) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS, strtotime($ui->getUserDateAdded()));
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('uName', t('Username'), array('UserSearchDefaultColumnSet', 'getUserName')));
		$this->addColumn(new DatabaseItemListColumn('uEmail', t('Email'), array('UserSearchDefaultColumnSet', 'getUserEmail')));
		$this->addColumn(new DatabaseItemListColumn('uDateAdded', t('Last Modified'), array('UserSearchDefaultColumnSet', 'getUserDateAdded')));
		$this->addColumn(new DatabaseItemListColumn('uNumLogins', t('# Logins'), 'getNumLogins')); 
		$date = $this->getColumnByKey('uDateAdded');
		$this->setDefaultSortColumn($date, 'desc');
	}
}
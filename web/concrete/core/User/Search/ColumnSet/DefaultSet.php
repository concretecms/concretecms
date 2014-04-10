<?php
namespace Concrete\Core\User\Search\ColumnSet;
use \Concrete\Core\Foundation\Collection\Database\Column\Column as DatabaseItemListColumn;
class DefaultSet extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'UserAttributeKey';	
	
	public function getUserName($ui) {
		return '<a data-user-name="' . $ui->getUserDisplayName() . '" data-user-email="' . $ui->getUserEmail() . '" data-user-id="' . $ui->getUserID() . '" href="#">' . $ui->getUserName() . '</a>';
	}

	public function getUserEmail($ui) {
		return '<a href="mailto:' . $ui->getUserEmail() . '">' . $ui->getUserEmail() . '</a>';
	}
	
	public static function getUserDateAdded($ui) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS, strtotime($ui->getUserDateAdded()));
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('uName', t('Username'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserName')));
		$this->addColumn(new DatabaseItemListColumn('uEmail', t('Email'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserEmail')));
		$this->addColumn(new DatabaseItemListColumn('uDateAdded', t('Last Modified'), array('Concrete\Core\User\Search\ColumnSet\DefaultSet', 'getUserDateAdded')));
		$this->addColumn(new DatabaseItemListColumn('uNumLogins', t('# Logins'), 'getNumLogins')); 
		$date = $this->getColumnByKey('uDateAdded');
		$this->setDefaultSortColumn($date, 'desc');
	}
}
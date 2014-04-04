<?php
namespace Concrete\Core\Page\Search\ColumnSet;
use Loader;
class Default extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'CollectionAttributeKey';	

	public static function getCollectionDatePublic($c) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDatePublic()));
	}

	public static function getCollectionDateModified($c) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDateLastModified()));
	}
	
	public function getCollectionAuthor($c) {
		$uID = $c->getCollectionUserID();
		$ui = UserInfo::getByID($uID);
		if (is_object($ui)) {
			return $ui->getUserName();
		}
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('ptHandle', t('Type'), 'getPageTypeName', false));
		$this->addColumn(new DatabaseItemListColumn('cvName', t('Name'), 'getCollectionName'));
		$this->addColumn(new DatabaseItemListColumn('cvDatePublic', t('Date'), array('PageSearchDefaultColumnSet', 'getCollectionDatePublic')));
		$this->addColumn(new DatabaseItemListColumn('cDateModified', t('Last Modified'), array('PageSearchDefaultColumnSet', 'getCollectionDateModified')));
		$this->addColumn(new DatabaseItemListColumn('author', t('Author'), array('PageSearchDefaultColumnSet', 'getCollectionAuthor'), false));
		$date = $this->getColumnByKey('cDateModified');
		$this->setDefaultSortColumn($date, 'desc');
	}
}
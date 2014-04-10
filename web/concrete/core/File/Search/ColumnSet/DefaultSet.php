<?php
namespace Concrete\Core\File\Search\ColumnSet;
use Loader;
use \Concrete\Core\Foundation\Collection\Database\Column\Column as DatabaseItemListColumn;

class DefaultSet extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'FileAttributeKey';	
	
	public static function getFileDateAdded($f) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES, strtotime($f->getDateAdded()));
	}

	public static function getFileDateActivated($f) {
		$fv = $f->getVersion();
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES, strtotime($fv->getDateAdded()));
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('fvType', t('Type'), 'getType', false));
		$this->addColumn(new DatabaseItemListColumn('fvTitle', t('Title'), 'getTitle'));
		$this->addColumn(new DatabaseItemListColumn('fDateAdded', t('Added'), array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateAdded')));
		$this->addColumn(new DatabaseItemListColumn('fvDateAdded', t('Active'), array('\Concrete\Core\File\Search\ColumnSet\DefaultSet', 'getFileDateActivated')));
		$this->addColumn(new DatabaseItemListColumn('fvSize', t('Size'), 'getSize'));
		$title = $this->getColumnByKey('fDateAdded');
		$this->setDefaultSortColumn($title, 'desc');
	}
}
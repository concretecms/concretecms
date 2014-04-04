<?php
namespace Concrete\Core\File\Search\ColumnSet;
class Available extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'FileAttributeKey';
	public function __construct() {
		parent::__construct();
		$this->addColumn(new DatabaseItemListColumn('fvAuthorName', t('Author'), 'getAuthorName'));
	}
}
<?php
namespace Concrete\Core\File\Search\ColumnSet;
use \Concrete\Core\Foundation\Collection\Database\Column\Column as DatabaseItemListColumn;
use Loader;
class Available extends DefaultSet {
	protected $attributeClass = 'FileAttributeKey';
	public function __construct() {
		parent::__construct();
		$this->addColumn(new DatabaseItemListColumn('fvAuthorName', t('Author'), 'getAuthorName'));
	}
}
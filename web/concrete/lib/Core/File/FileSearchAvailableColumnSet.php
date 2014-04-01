<?php
namespace Concrete\Core\File;
class FileSearchAvailableColumnSet extends FileSearchDefaultColumnSet {
	protected $attributeClass = 'FileAttributeKey';
	public function __construct() {
		parent::__construct();
		$this->addColumn(new DatabaseItemListColumn('fvAuthorName', t('Author'), 'getAuthorName'));
	}
}
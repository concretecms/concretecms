<?php
namespace Concrete\Core\Page\Search\ColumnSet;
class Available extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'CollectionAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}
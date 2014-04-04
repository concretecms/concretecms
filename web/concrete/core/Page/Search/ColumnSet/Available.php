<?php
namespace Concrete\Core\Page\Search\ColumnSet;
use Loader;
class Available extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'CollectionAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}
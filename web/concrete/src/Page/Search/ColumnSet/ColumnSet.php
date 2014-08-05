<?php
namespace Concrete\Core\Page\Search\ColumnSet;
use Loader;
use \Concrete\Core\Search\Column\Set;
use User;
class ColumnSet extends Set {
	protected $attributeClass = 'CollectionAttributeKey';
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('PAGE_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof Set)) {
			$fldc = new DefaultSet();
		}
		return $fldc;
	}
}
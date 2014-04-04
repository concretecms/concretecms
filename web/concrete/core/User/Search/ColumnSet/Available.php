<?php
namespace Concrete\Core\User\Search\ColumnSet;
class Available extends \Concrete\Core\Foundation\Collection\Database\Column\Set {
	protected $attributeClass = 'UserAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}

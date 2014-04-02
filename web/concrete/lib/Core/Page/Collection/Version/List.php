<?php
namespace Concrete\Core\Page\Collection\Version;
use \Concrete\Core\Foundation\Collection\Database\DatabaseItemList;

/**
 * An object that holds a list of versions for a particular collection.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class List extends DatabaseItemList {

	public function __construct($c) {
		$this->c = $c;
		$this->setQuery('select cvID from CollectionVersions');
		$this->filter('cID', $c->getCollectionID());
		$this->sortBy('cvID', 'desc');			
	}

	public function get($itemsToGet, $offset) {
		$r = parent::get($itemsToGet, $offset);
		$items = array();
		foreach($r as $row) {
			$cv = CollectionVersion::get($this->c, $row['cvID']);
			$items[] = $cv;
		}
		return $items;
	}	

}
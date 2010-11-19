<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for use with the C5 dashboard.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteDashboardHelper {

	/** 
	 * Checks to see if a user has access to the C5 dashboard.
	 */
	public function canRead() {
		$c = Page::getByPath('/dashboard', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canRead();
	}
	


	
}
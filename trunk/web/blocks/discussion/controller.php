<?
/**
 * @package Blocks
 * @subpackage BlockTypes
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * Controller for the discussion block, which allows site owners to add threaded discussions and forums to their site.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class DiscussionBlockController extends BlockController {
		
		protected $btDescription = "Adds a forum to a particular area of your site.";
		protected $btName = "Discussion";
		protected $btTable = 'btDiscussion';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "300";	
		
	}
	
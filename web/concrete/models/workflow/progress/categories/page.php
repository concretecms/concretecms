<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class PageWorkflowProgress extends WorkflowProgress {  
	
	protected $cID;

	public static function add(Workflow $wf, PageWorkflowRequest $wr) {
		$wp = parent::add($wf, $wr);
		$db = Loader::db();
		$db->Replace('PageWorkflowProgress', array('cID' => $wr->getRequestedPageID(), 'wpID' => $wp->getWorkflowProgressID()), array('cID', 'wpID'), true);
		$wp->cID = $wr->getRequestedPageID();
		return $wp;
	}
}

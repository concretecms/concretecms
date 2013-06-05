<?
defined('C5_EXECUTE') or die("Access Denied.");
$p = Page::getByPath('/dashboard/workflow/list');
$cp = new Permissions($p);
if ($cp->canViewPage()) { 
	$workflow = Workflow::getByID($_REQUEST['wfID']);
	Loader::element('permission/details/basic_workflow', array('workflow' => $workflow));
}

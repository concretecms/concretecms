<?
defined('C5_EXECUTE') or die("Access Denied.");
$ch = Page::getByPath('/dashboard/composer/list', 'RECENT');
$chp = new Permissions($ch);
if ($_REQUEST['cmpID'] > 0) {
	$cmp = Composer::getByID($_REQUEST['cmpID']);
	$fsp = new Permissions($fs);
	if ($chp->canViewPage()) {
		Loader::element('permission/details/composer', array("composer" => $cmp));
	}
}

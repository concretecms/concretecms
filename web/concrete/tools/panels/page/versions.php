<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canViewPageVersions()) { ?>

		<section>
			<header>&lt; <?=t('Versions')?></header>
			<br/><br/>
		</section>


	<? }
}
?>
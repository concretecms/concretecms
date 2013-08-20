<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canViewPageVersions()) { ?>

		<section>
			<header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Versions')?></header>
					<menu><li><a href="#" data-launch-sub-panel="page/versions"><?=t('Version 2')?></a></li></menu>

		</section>


	<? }
}
?>
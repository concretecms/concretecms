<?
defined('C5_EXECUTE') or die("Access Denied.");
$pagetypes = PageType::getList();
$ptlist = array();
foreach($pagetypes as $pt) {
	$ptp = new Permissions($pt);
	if ($ptp->canComposePageType()) {
		$ptlist[] = $pt;
	}
}

$drafts = Page::getDrafts();
$mydrafts = array();
foreach($drafts as $d) {
	$dp = new Permissions($d);
	if ($dp->canEditPage()) {
		$mydrafts[] = $d;
	}
}
?>

<div class="ccm-panel-content-inner">

<? if (count($ptlist)) {?>
<h5><?=t('New Page')?></h5>
<ul class="ccm-panel-sitemap-list">
<? foreach($ptlist as $pt) { ?> 
	<li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/draft/create?ptID=<?=$pt->getPageTypeID()?>"><?=$pt->getPageTypeName()?></a></li>
<? } ?>
</ul>
<? } ?>

<?
$v = View::getInstance();
$v->requireAsset('core/sitemap');
$sh = Loader::helper('concrete/dashboard/sitemap');
if ($sh->canRead()) { ?>	

<h5><?=t('Sitemap')?></h5>
<div id="ccm-sitemap-panel-sitemap"></div>

<script type="text/javascript">
$(function() {
	$('#ccm-sitemap-panel-sitemap').ccmsitemap({
		onSelectNode: function(node) {
			window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + node.data.cID;
		}
	});
});
</script>

<? } ?>

<? if (count($mydrafts)) {?>
<h5><?=t('Page Drafts')?></h5>
<ul class="ccm-panel-sitemap-list">
<? foreach($mydrafts as $dc) { 
	?> 
	<li><a href="<?=View::url('/dashboard/composer/write', 'draft', $dc->getCollectionID())?>"><?
		if ($dc->getCollectionName()) {
			print $dc->getCollectionName();
		} else {
			print t('(Untitled)');
		}
	?></a></li>
<? } ?>
</ul>
<? } ?>


</div>


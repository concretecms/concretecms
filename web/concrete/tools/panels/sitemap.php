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

$drafts = PageDraft::getList();
$mydrafts = array();
foreach($drafts as $d) {
	$dp = new Permissions($d);
	if ($dp->canEditPageDraft()) {
		$mydrafts[] = $d;
	}
}
?>

<div class="ccm-panel-content-inner">

<? if (count($ptlist)) {?>
<h5><?=t('New Page')?></h5>
<ul class="ccm-panel-sitemap-list">
<? foreach($ptlist as $pt) { ?> 
	<li><a href="<?=View::url('/dashboard/composer/write', 'composer', $pt->getPageTypeID())?>"><?=$pt->getPageTypeName()?></a></li>
<? } ?>
</ul>
<? } ?>

<?
$r = Request::get();
$r->requireAsset('core/sitemap');
$sh = Loader::helper('concrete/dashboard/sitemap');
if ($sh->canRead()) { ?>	

<h5><?=t('Sitemap')?></h5>
<div id="ccm-sitemap-panel-sitemap"></div>

<script type="text/javascript">
$(function() {
	$('#ccm-sitemap-panel-sitemap').ccmsitemap();
	ccm_event.subscribe('SitemapSelectNode', function(event) {
		window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + event.eventData.node.data.cID;
	});
});
</script>

<? } ?>

<? if (count($mydrafts)) {?>
<h5><?=t('Page Drafts')?></h5>
<ul class="ccm-panel-sitemap-list">
<? foreach($mydrafts as $d) { 
	$dc = $d->getPageDraftCollectionObject();
	?> 
	<li><a href="<?=View::url('/dashboard/composer/write', 'draft', $d->getPageDraftID())?>"><?=$dc->getCollectionName()?></a></li>
<? } ?>
</ul>
<? } ?>


</div>


<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-panel-content-inner">

<? if (count($pagetypes)) {?>
	<h5><?=t('New Page')?></h5>
	<ul class="ccm-panel-sitemap-list">
	<? foreach($pagetypes as $pt) { ?> 
		<li><a href="<?=URL::to('/ccm/system/page/', 'create', $pt->getPageTypeID())?>"><?=$pt->getPageTypeName()?></a></li>
	<? } ?>
	</ul>
<? } ?>

<?
if ($canViewSitemap) { ?>	
	<h5><?=t('Sitemap')?></h5>
	<div id="ccm-sitemap-panel-sitemap"></div>
	<script type="text/javascript">
	$(function() {
		$('#ccm-sitemap-panel-sitemap').concreteSitemap({
			onSelectNode: function(node) {
				window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + node.data.cID;
			}
		});
	});
	</script>
<? } ?>

<? if (count($drafts)) {?>
	<h5><?=t('Page Drafts')?></h5>
	<ul class="ccm-panel-sitemap-list">
	<? foreach($drafts as $dc) { 
		?> 
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($dc)?>"><?
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


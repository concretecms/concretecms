<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-panel-content-inner">

<? if (count($frequentPageTypes) || count($otherPageTypes)) {?>
	<h5><?=t('New Page')?></h5>
	<ul class="ccm-panel-sitemap-list">
	<? foreach($frequentPageTypes as $pt) { ?>
		<li><a href="<?=URL::to('/ccm/system/page/', 'create', $pt->getPageTypeID())?>"><?=$pt->getPageTypeDisplayName()?></a></li>
	<? } ?>
    <? foreach($otherPageTypes as $pt) { ?>
        <li data-page-type="other" <? if (count($frequentPageTypes)) { ?>style="display: none"<? } ?>><a href="<?=URL::to('/ccm/system/page/', 'create', $pt->getPageTypeID())?>"><?=$pt->getPageTypeDisplayName()?></a></li>
    <? } ?>

    <? if (count($frequentPageTypes) && count($otherPageTypes)) { ?>
        <li class="ccm-panel-sitemap-more-page-types"><a href="#" data-sitemap="show-more"><i class="fa fa-caret-down"></i> <?=t('More')?></a></li>
    <? } ?>
	</ul>

    <script type="text/javascript">
    $(function() {
        $('a[data-sitemap=show-more]').on('click', function() {
            $('li[data-page-type=other]').show();
            $(this).parent().remove();
        });
    });
    </script>
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


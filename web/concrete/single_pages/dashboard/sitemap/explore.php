<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<style type="text/css">
div.ccm-sitemap-explore ul li.ccm-sitemap-explore-paging {display: none;}
</style>

<script type="text/javascript">
	$(function() {
		$('div#ccm-flat-sitemap-container').concreteSitemap({
			displayNodePagination: true,
			cParentID: '<?=$nodeID?>',
			displaySingleLevel: true
		});
	});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sitemap'), t('Sitemap flat view lets you page through particular long lists of pages.'), 'span10 offset1', false);?>
<div class="ccm-pane-body">

<? if ($dh->canRead()) { ?>	

	<div id="ccm-flat-sitemap-container"></div>

<? } else { ?>
	<p><?=t('You do not have access to the dashboard sitemap.')?></p>
<? } ?>

</div>	
<div class="ccm-pane-footer" id="ccm-explore-paging-footer">
	
</div>

<script type="text/javascript">
$(function() {
	$('#ccm-explore-paging-footer').html($('li.ccm-sitemap-explore-paging').html());
});
</script>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
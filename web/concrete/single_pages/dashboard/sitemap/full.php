<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
?>

<script type="text/javascript">
	$(function() {
		$('div#ccm-full-sitemap-container').ccmsitemap({
		});
	});
</script>

<header><?=t('Sitemap')?></header>

<? /*
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sitemap'), t('The sitemap allows you to view your site as a tree and easily organize its hierarchy.'), 'span10 offset1', false);?>
<div class="ccm-pane-options">
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?>open<? } else { ?>closed<? } ?>"><?=t('Options')?></a>
	<div class="ccm-pane-options-content" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> style="display: block" <? } ?>>
		<label for="ccm-show-all-pages-cb" class="checkbox"><?=t('Show System Pages')?>
			<input type="checkbox" id="ccm-show-all-pages-cb" <? if ($_SESSION['dsbSitemapShowSystem'] == 1) { ?> checked <? } ?> />
		</label>
	</div>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">

	<? $u = new User();
	if ($u->isSuperUser()) {
		if (Queue::exists('copy_page')) {
		$q = Queue::get('copy_page');
		if ($q->count() > 0) { ?>

			<div style="position: relative" class="alert alert-warning">
				<div style="position: absolute; top: 5px; right: 5px"><button class="btn btn-small" onclick="$.fn.ccmsitemap('refreshCopyOperations')"><?=t('Resume Copy')?></button></div>
				<?=t('Page copy operations pending.')?>
			</div>

		<? }
	}

	} ?>

	<? if ($sh->canRead()) { ?>	
		
		<div id="ccm-full-sitemap-container"></div>
		
	
	<? } else { ?>
	
		<p><?=t("You do not have access to the sitemap.");?></p>
	
	<? } ?>
</div>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
*/
?>


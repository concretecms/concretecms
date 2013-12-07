<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
?>

<script type="text/javascript">
	$(function() {
		$('div#ccm-full-sitemap-container').concreteSitemap({
		});
	});
</script>


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

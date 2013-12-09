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

		<div class="alert alert-warning">
			<?=t('Page copy operations pending.')?>
			<button class="btn btn-xs btn-default pull-right" onclick="ConcreteSitemap.refreshCopyOperations()"><?=t('Resume Copy')?></button>
		</div>

	<? }
}

} ?>

<? if ($sh->canRead()) { ?>	
	
	<div id="ccm-full-sitemap-container"></div>
	

<? } else { ?>

	<p><?=t("You do not have access to the sitemap.");?></p>

<? } ?>

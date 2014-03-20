<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
?>

<div class="ccm-dashboard-content-full">
<script type="text/javascript">
	$(function() {
		$('div#ccm-full-sitemap-container').concreteSitemap({

		});
	});
</script>

<style type="text/css">
div#ccm-full-sitemap-container {
	margin-left: 95px;
}
</style>

<form action="<?=URL::to('/dashboard/sitemap/search')?>"  class="form-inline ccm-search-fields-none ccm-search-fields">
	<div class="ccm-search-fields-row">
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="glyphicon glyphicon-search"></i>
			<?=$form->search('cvName', array('placeholder' => t('Name')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="<?=URL::to('/dashboard/sitemap/search')?>"><?=t('Advanced Search')?></a>
	</ul>
	</div>
</form>


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
	

	<hr/>
<? } else { ?>

	<p><?=t("You do not have access to the sitemap.");?></p>

<? } ?>

</div>

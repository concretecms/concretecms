<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="newsflow-latest-edition">
<h3><?=t('Latest News')?></h3>
<h5><?=$editionTitle?></h5>
<p><small><?=date(DATE_APP_GENERIC_MDY_FULL, strtotime($editionDate))?></small></p>
<p><?=$editionDescription?></p>
<a href="javascript:void(0)" onclick="ccm_showNewsflowOffsite(<?=$editionID?>)" class="btn"><?=t('Read On')?></a>
</div>

<div class="newsflow-paging-next"><span><a href="javascript:void(0)" onclick="ccm_showNewsflowOffsite(<?=$editionID?>)"></a></span></div>

<script type="text/javascript">
$(function() {
	ccm_setNewsflowPagingArrowHeight();
	$('#newsflow-latest-edition').parent().addClass('newsflow-latest-edition-wrapper');
});
</script>
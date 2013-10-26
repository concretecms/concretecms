<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<section id="ccm-panel-page-attributes">
	<header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Versions')?></header>
	<div class="ccm-panel-page-attributes-search">
		<i class="glyphicon glyphicon-search"></i>
		<input type="text" name="" id="ccm-panel-page-attributes-search-input" placeholder="<?=t('Search')?>" autocomplete="false" />
	</div>

	<div class="ccm-panel-content-inner" id="ccm-panel-page-attributes-list">
	<? foreach($attributes as $set) { ?>
		<div class="ccm-panel-page-attributes-set">
			<h5><?=$set->title?></h5>
			<ul>
			<? foreach($set->attributes as $key) { ?>
				<li><a data-attribute-key="<?=$key->getAttributeKeyID()?>" href="#"><?=$key->getAttributeKeyName()?></a></li>
			<? } ?>
			</ul>
		</div>
	<? } ?>
	</div>

</section>


<script type="text/javascript">
$(function() {
	$('#ccm-panel-page-attributes-search-input').liveUpdate('ccm-panel-page-attributes-list', 'attributes');
	$('#ccm-panel-page-attributes').on('click', 'a[data-attribute-key]:not(.ccm-panel-page-attribute-selected)', function() {
		$(this).toggleClass('ccm-panel-page-attribute-selected');
		CCMPageAttributeDetail.addAttributeKey($(this).attr('data-attribute-key'));
	});
});
</script>

<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<section id="ccm-panel-page-attributes">
	<header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Attributes')?></header>
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
				<li><a data-attribute-key="<?=$key->getAttributeKeyID()?>" <? if (in_array($key->getAttributeKeyID(), $selectedAttributeIDs)) { ?>class="ccm-panel-page-attribute-selected" <? } ?> href="#"><?=$key->getAttributeKeyName()?></a></li>
			<? } ?>
			</ul>
		</div>
	<? } ?>
	</div>

</section>


<script type="text/javascript">
ConcretePanelPageAttributes = {

	selectAttributeKey: function(akID) {
		$attribute = $('a[data-attribute-key=' + akID + ']');
		$attribute.addClass('ccm-panel-page-attribute-selected');
		ConcretePanelPageAttributesDetail.addAttributeKey(akID);
	},

	deselectAttributeKey: function(akID) {
		$attribute = $('a[data-attribute-key=' + akID + ']');
		$attribute.removeClass('ccm-panel-page-attribute-selected');
	},

}
$(function() {
	$('#ccm-panel-page-attributes-search-input').liveUpdate('ccm-panel-page-attributes-list', 'attributes');
	$('#ccm-panel-page-attributes').on('click', 'a[data-attribute-key]:not(.ccm-panel-page-attribute-selected)', function() {
		ConcretePanelPageAttributes.selectAttributeKey($(this).attr('data-attribute-key'));
	});
});
</script>

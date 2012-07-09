<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
<form id="ccm-marketplace-browser-form" method="get" action="<?=$action?>" class="form-horizontal">
<div class="ccm-pane-options-permanent-search">
	<?=Loader::helper('form')->hidden('_ccm_dashboard_external')?>
	<div class="span4">
	<?=$form->label('marketplaceRemoteItemKeywords', t('Keywords'))?>
	<div class="controls">
		<?=$form->text('marketplaceRemoteItemKeywords', array('style' => 'width: 140px'))?>
	</div>
	</div>
	
	<div class="span4">
	<?=$form->label('marketplaceRemoteItemSetID', t('Category'))?>
	<div class="controls">
	<?=$form->select('marketplaceRemoteItemSetID', $sets, $selectedSet, array('style' => 'width: 150px'))?>
	</div>
	</div>


	<div class="span2">
		<?=$form->submit('submit', t('Search'))?>
	</div>
</div>
<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<? if ($_REQUEST['marketplaceRemoteItemSortBy'] || $_REQUEST['marketplaceIncludeOnlyCompatibleAddons']) { ?>open<? } else{ ?>closed<? } ?>"><?=t('More Options')?></a>

<div class="control-group ccm-pane-options-content" <? if ($_REQUEST['marketplaceRemoteItemSortBy'] || $_REQUEST['marketplaceIncludeOnlyCompatibleAddons']) { ?>style="display: block" <? } ?>>
	<br/>
	<table class="table table-striped ccm-search-advanced-fields">
	<tr>
		<th colspan="2" width="100%"><?=t('Additional Filters')?></th>
	</tr>
	<tr>
		<td style="white-space: nowrap"><?=t('Sort By')?></td>
		<td width="100%"><?=$form->select('marketplaceRemoteItemSortBy', $sortBy, $selectedSort, array('style' => 'display: inline; margin-left: 0px; width: 150px'))?></td>
	</tr>
	<tr>
		<td style="white-space: nowrap"><?=t('Compatibility')?></td>
		<td width="100%"><label style="display: block; float: none; text-align: left; width: auto">
	<?=$form->checkbox('marketplaceIncludeOnlyCompatibleAddons', 1)?>
		<span><?=t('Include only add-ons compatible with my version of concrete5.')?></span>
	</label>
	</td>
	</tr>
	</table>
	
</div>	

</form>	
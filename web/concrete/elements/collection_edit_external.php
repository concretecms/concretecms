<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<? 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() && $c->getCollectionPointerExternalLink() != '') {
?>

	<form class="form-stacked" method="post" action="<?=$c->getCollectionAction()?>" id="ccmEditLink">		
	
	<div class="ccm-form-area">
	<div class="ccm-field">
	
	<label><?=t('Name')?></label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="text" style="width: 95%" />
	
	</div>
	<div class="ccm-field">

	<label><?=t('URL')?></label> <input type="text" name="cExternalLink" style="width: 95%" value="<?=$c->getCollectionPointerExternalLink()?>" />

	</div>

	<div class="ccm-field">

	<label for="cExternalLinkNewWindow"><input type="checkbox" value="1" <? if ($c->openCollectionPointerExternalLinkInNewWindow()) { ?> checked <? } ?> name="cExternalLinkNewWindow" id="cExternalLinkNewWindow" style="vertical-align: middle" /> <?=t('Open Link in New Window')?></label>

	</div>
	
	<div class="ccm-spacer">&nbsp;</div>
	</div>


	<div class="ccm-buttons dialog-buttons">
	<input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
	<a href="javascript:void(0)" onclick="$('#ccmEditLink').get(0).submit()" class="btn primary ccm-button-right accept"><span><?=('Save')?></span></a>
	</div>	
	<input type="hidden" name="update_external" value="1" />
	<input type="hidden" name="processCollection" value="1">

</form>
<? } ?>
</div>

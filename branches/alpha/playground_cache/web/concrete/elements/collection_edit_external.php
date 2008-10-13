<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="ccm-pane-controls">
<? 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() && $c->getCollectionPointerExternalLink() != '') {
?>

<h1>Edit External Link</h1>

	<form method="post" action="<?=$c->getCollectionAction()?>" id="ccmEditLink">		
	
	<div class="ccm-form-area">
	<div class="ccm-field">
	
	<label>Display Name</label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="text" style="width: 100%" />
	
	</div>
	<div class="ccm-field">

	<label>URL</label> <input type="text" name="cExternalLink" style="width: 100%" value="<?=$c->getCollectionPointerExternalLink()?>" />

	</div>
	
	<div class="ccm-spacer">&nbsp;</div>
	</div>


	<div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="$('#ccmEditLink').get(0).submit()" class="ccm-button-right accept"><span>Update Link</span></a>
	</div>	
	<input type="hidden" name="update_external" value="1" />
	<input type="hidden" name="processCollection" value="1">

</form>
<? } ?>
</div>

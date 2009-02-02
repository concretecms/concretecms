<?
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;
Loader::model('collection_types');
Loader::model('collection_attributes');
$dt = Loader::helper('form/date_time');

if ($cp->canAdminPage()) {
	$ctArray = CollectionType::getList();
}
?>
<div class="ccm-pane-controls">
<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?=$c->getCollectionAction()?>">
			<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

	<script type="text/javascript"> 
		
		function ccm_triggerSelectUser(uID, uName) {
			$('#ccm-uID').val(uID);
			$('#ccm-uName').html(uName);
		}
		

	</script>
	
	<style>
	.ccm-field-meta #newAttrValueRows{ margin-top:4px; }
	.ccm-field-meta .newAttrValueRow{margin-top:4px}	
	.ccm-field-meta input.faint{ color:#999 }
	</style>
	
	<h1><?=t('Page Properties')?></h1>
	
	<div id="ccm-required-meta">
	<h2><?=t('Standard Information')?></h2></td>

	
	<div class="ccm-field-one">
	<label><?=t('Name')?></label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="ccm-input-text">
	</div>
	
	<div class="ccm-field-two">
	<label><?=t('Alias')?></label> <? if (!$c->isGeneratedCollection()) { ?><input s type="text" name="cHandle" class="ccm-input-text" value="<?=$c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?=$c->getCollectionHandle()?>"><? } else { ?><?=$c->getCollectionHandle()?><? } ?>
	</div>
	
	
	<div class="ccm-field-one">
	
	<label><?=t('Public Date/Time')?></label> 
	<? 
	print $dt->datetime('cDatePublic', $c->getCollectionDatePublic()); ?>
	</div>
	
	<div class="ccm-field-two">
	<label><?=t('Owner')?></label>
		<? 
		$ui = UserInfo::getByID($c->getCollectionUserID());
		if (is_object($ui)) {
			$currentUName = $ui->getUserName();
		} else {
			$currentUName = "(None)";
		}
		print '<div style="padding-top: 4px;font-size: 12px"><span id="ccm-uName">' . $currentUName . '</span>';
		if ($cp->canAdminPage()) { ?>
		(<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_user.php" id="ccm-edit-page-user" dialog-modal="false" dialog-width="600" dialog-height="400" dialog-title="<?=t('Choose User')?>"><?=t('Edit')?></a>)
		<input type="hidden" name="uID" value="<?=$c->getCollectionUserID()?>" id="ccm-uID" />
		
		<script type="text/javascript">$(function() {
			$("#ccm-edit-page-user").dialog();
		})</script>
		<? } ?>
		</div>

	</div>
		
	
	<div class="ccm-field">
	<label><?=t('Description')?></label> <textarea name="cDescription" class="ccm-input-text" style="width: 570px; height: 50px"><?=$c->getCollectionDescription()?></textarea>
	</div>
	
	<? Loader::element('collection_metadata_fields', array('c'=>$c ) ); ?>
	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="$('#ccmMetadataForm').get(0).submit()" class="ccm-button-right accept"><span><?=t('Save')?></span></a>
	</div>	

<? Loader::element('block_al'); ?>
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
		
		
		var ccm_activePropertiesTab = "ccm-page-properties-standard";
		
		$("#ccm-properties-tabs a").click(function() {
			$("li.ccm-nav-active").removeClass('ccm-nav-active');
			$("#" + ccm_activePropertiesTab + "-tab").hide();
			ccm_activePropertiesTab = $(this).attr('id');
			$(this).parent().addClass("ccm-nav-active");
			$("#" + ccm_activePropertiesTab + "-tab").show();
		});
	</script>
	
	<style>
	.ccm-field-meta #newAttrValueRows{ margin-top:4px; }
	.ccm-field-meta .newAttrValueRow{margin-top:4px}	
	.ccm-field-meta input.faint{ color:#999 }
	</style>
	
	<h1><?=t('Page Properties')?></h1>

	
	<div id="ccm-required-meta">
	
		
	<ul class="ccm-dialog-tabs" id="ccm-properties-tabs">
		<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-page-properties-standard"><?=t('Standard Properties')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-page-paths"><?=t('Page Paths and Location')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-properties-custom"><?=t('Custom Fields')?></a></li>
	</ul>

	<div id="ccm-page-properties-standard-tab">
	
	<div class="ccm-field-one">
	<label><?=t('Name')?></label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="ccm-input-text">
	</div>
	
	<div class="ccm-field-two">
	<label><?php echo t('Alias')?></label> <?php  if (!$c->isGeneratedCollection()) { ?><input s type="text" name="cHandle" class="ccm-input-text" value="<?php echo $c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?php echo $c->getCollectionHandle()?>"><?php  } else { ?><?php echo $c->getCollectionHandle()?><?php  } ?>
	</div>
	
	<div class="ccm-field-one">
      <p>&nbsp;</p>
	</div>

	<div class="ccm-field-two">
	<label><?=t('Additional Page URL(s)')?></label> <?
if (!$c->isGeneratedCollection()) { 
	$paths = $c->getPagePaths();
    echo '<div>';
	foreach ($paths as $path) {
		if (!$path['ppIsCanonical']) {
			$ppID = $path['ppID'];
			$cPath = $path['cPath'];
			echo '<span>' .
			     '<input type="text" name="ppURL-' . $ppID . '" class="ccm-input-text-narrow" value="' . $cPath . '" id="ppID-'. $ppID . '"> ' .
			     '<a onclick="ccm_delListEl(this)" href="javascript:void(0)">-</a>' . '<br /></span>'."\n";
		}
	}
	echo '<span>' .
	     '<input type="text" name="ppURL-add-0" class="ccm-input-text-narrow" value="" id="ppID-add-0"> ' .
		 '<a onclick="ccm_addListEl(this)" href="javascript:void(0)">+</a></span>';
    echo '</div>';
}
?>
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
	
	</div>
	
	<div id="ccm-page-paths-tab" style="display: none">
	asdflkj
	</div>
	
	<div id="ccm-properties-custom-tab" style="display: none">
		<? Loader::element('collection_metadata_fields', array('c'=>$c ) ); ?>
	</div>
	
	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="$('#ccmMetadataForm').get(0).submit()" class="ccm-button-right accept"><span><?=t('Save')?></span></a>
	</div>

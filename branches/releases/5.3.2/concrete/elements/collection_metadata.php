<?php 
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
<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?php echo $c->getCollectionAction()?>">
			<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />

	<script type="text/javascript"> 
		
		function ccm_triggerSelectUser(uID, uName) {
			$('#ccm-uID').val(uID);
			$('#ccm-uName').html(uName);
		}
		
		
		var ccm_activePropertiesTab = "ccm-properties-standard";
		
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
	
	#ccm-properties-custom-tab, #ccm-properties-standard-tab, #ccm-page-paths-tab {
		margin-top: 12px;
	}
	</style>
	
	<h1><?php echo t('Page Properties')?></h1>

	
	<div id="ccm-required-meta">
	
		
	<ul class="ccm-dialog-tabs" id="ccm-properties-tabs">
		<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-properties-standard"><?php echo t('Standard Properties')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-page-paths"><?php echo t('Page Paths and Location')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-properties-custom"><?php echo t('Custom Fields')?></a></li>
	</ul>

	<div id="ccm-properties-standard-tab">
	
	<div class="ccm-field-one">
	<label><?php echo t('Name')?></label> <input type="text" name="cName" value="<?php echo htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" class="ccm-input-text">
	</div>
	
	<div class="ccm-field-one">
	
	<label><?php echo t('Public Date/Time')?></label> 
	<?php  
	print $dt->datetime('cDatePublic', $c->getCollectionDatePublic()); ?>
	</div>
	
	<div class="ccm-field-two">
	<label><?php echo t('Owner')?></label>
		<?php  
		$ui = UserInfo::getByID($c->getCollectionUserID());
		if (is_object($ui)) {
			$currentUName = $ui->getUserName();
		} else {
			$currentUName = "(None)";
		}
		print '<div style="padding-top: 4px;font-size: 12px"><span id="ccm-uName">' . $currentUName . '</span>';
		if ($cp->canAdminPage()) { ?>
		(<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_user.php" id="ccm-edit-page-user" dialog-modal="false" dialog-width="600" dialog-height="400" dialog-title="<?php echo t('Choose User')?>"><?php echo t('Edit')?></a>)
		<input type="hidden" name="uID" value="<?php echo $c->getCollectionUserID()?>" id="ccm-uID" />
		
		<script type="text/javascript">$(function() {
			$("#ccm-edit-page-user").dialog();
		})</script>
		<?php  } ?>
		</div>

	</div>
		
	
	<div class="ccm-field">
	<label><?php echo t('Description')?></label> <textarea name="cDescription" class="ccm-input-text" style="width: 570px; height: 50px"><?php echo $c->getCollectionDescription()?></textarea>
	</div>
	
	</div>
	
	<div id="ccm-page-paths-tab" style="display: none">
		
		<div class="ccm-field">
		<label><?php echo  t('Canonical URL')?></label>
		<?php  if (!$c->isGeneratedCollection()) { ?>
			<?php echo BASE_URL . substr($c->getCollectionPath(), 0, -1 * strlen($c->getCollectionHandle()))?><input s type="text" name="cHandle" class="ccm-input-text" value="<?php  echo $c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?php  echo $c->getCollectionHandle()?>"><br /><br />
		<?php   } else { ?>
			<?php  echo $c->getCollectionHandle()?><br /><br />
		<?php   } ?>
		<div class="ccm-note"><?php echo t('This page must always be available from at least one URL. That URL is listed above.')?></div>
		</div>

		<?php  if (!$c->isGeneratedCollection()) { ?>
			<label><?php echo  t('Additional Page URL(s)') ?></label>
	
			<div class="ccm-field">
			<?php 
				$paths = $c->getPagePaths();
				foreach ($paths as $path) {
					if (!$path['ppIsCanonical']) {
						$ppID = $path['ppID'];
						$cPath = $path['cPath'];
						echo '<span class="ccm-meta_path">' .
			     			'<input type="text" name="ppURL-' . $ppID . '" class="ccm-input-text" value="' . $cPath . '" id="ppID-'. $ppID . '"> ' .
			     			'<a href="javascript:void(0)" class="ccm-meta-path-del">' . t('Remove Path') . '</a></span>'."\n";
					}
				}
			?>
		    <span class="ccm-meta-path">
	     		<input type="text" name="ppURL-add-0" class="ccm-input-text" value="" id="ppID-add-0">
		 		<a href="javascript:void(0)" class="ccm-meta-path-add"><?php echo t('Add Path')?></a>
			</span>
			</div>
		<?php  } ?>
	
	</div>
	
	<div id="ccm-properties-custom-tab" style="display: none">
		<?php  Loader::element('collection_metadata_fields', array('c'=>$c ) ); ?>
	</div>
	
	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="$('#ccmMetadataForm').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Save')?></span></a>
	</div>

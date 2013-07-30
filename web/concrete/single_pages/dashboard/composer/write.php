<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?

if (isset($entry)) { 

	$pk = PermissionKey::getByHandle('edit_page_properties');
	$pk->setPermissionObject($entry);
	$asl = $pk->getMyAssignment();
	$allowedAKIDs = $asl->getAttributesAllowedArray();

	$pk = PermissionKey::getByHandle('approve_page_versions');
	$pk->setPermissionObject($entry);
	$pa = $pk->getPermissionAccessObject();
	$workflow = (count($pa->getWorkflows()) > 0);

	?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(ucfirst($action) . ' ' . $ct->getCollectionTypeName(), false, false, false)?>
	<form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?=$this->action('save')?>" id="ccm-dashboard-composer-form">
	<input type="hidden" name="ccm-publish-draft" value="0" />

	<div class="ccm-pane-body">
	

	<div id="composer-save-status"></div>
	
	<fieldset>
	<legend><?=t("Basic Information")?></legend>
	<? if ($asl->allowEditName()) { ?>
	<div class="control-group">
		<?=$form->label('cName', t('Name'))?>
		<div class="controls"><?=$form->text('cName', Loader::helper("text")->entities($name), array('class' => 'input-xlarge', 'onKeyUp' => "ccm_updateAddPageHandle()"))?></div>		
	</div>
	<? } ?>
	
	<? if ($asl->allowEditPaths()) { ?>
	<div class="control-group">
		<?=$form->label('cHandle', t('URL Slug'))?>
		<div class="controls"><?=$form->text('cHandle', $handle, array('class' => 'span3'))?>
		<img src="<?=ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" id="ccm-url-slug-loader" style="display: none" />
		</div>		
	</div>
	<? } ?>

	<? if ($asl->allowEditDescription()) { ?>
	<div class="control-group">
		<?=$form->label('cDescription', t('Short Description'))?>
		<div class="controls"><?=$form->textarea('cDescription', Loader::helper("text")->entities($description), array('class' => 'input-xlarge', 'rows' => 5))?></div>		
	</div>
	<? } ?>

	<? if ($asl->allowEditDateTime()) { ?>
	<div class="control-group">
		<?=$form->label('cDatePublic', t('Date Posted'))?>
		<div class="controls"><? 
		if ($this->controller->isPost()) { 	
			$cDatePublic = Loader::helper('form/date_time')->translate('cDatePublic');
		}
		?><?=Loader::helper('form/date_time')->datetime('cDatePublic', $cDatePublic)?></div>		
	</div>
<? } ?>

	</fieldset>
	
	<? if ($entry->isComposerDraft()) { ?>
	<fieldset>
	<legend><?=t('Publish Location')?></legend>
	<div class="control-group">
		<span id="ccm-composer-publish-location"><?
		print $this->controller->getComposerDraftPublishText($entry);
		?>
		</span>
		
		<? 
	
	if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
		
		<a href="javascript:void(0)" onclick="ccm_openComposerPublishTargetWindow(false)"><?=t('Choose publish location.')?></a>
	
	<? } 
	
	?></div>
	</fieldset>
	<? } ?>
	
	<fieldset>
	<legend><?=t('Attributes &amp; Content')?></legend>
	<? 
	foreach($contentitems as $ci) {
		if ($ci instanceof AttributeKey) { 
			$ak = $ci;
			if (!in_array($ak->getAttributeKeyID(), $allowedAKIDs)) {
				continue;
			}
			
			if (is_object($entry)) {
				$value = $entry->getAttributeValueObject($ak);
			}
			?>
			<div class="control-group">
				<?=$ak->render('label');?>
				<div class="controls">
					<?=$ak->render('composer', $value, true)?>
				</div>
			</div>
		
		<? } else { 
			$b = $ci; 
			$b = $entry->getComposerBlockInstance($b);
			?>
		
		<div class="control-group">
		<?
		if (is_object($b)) {
			$bv = new BlockView();
			$bv->render($b, 'composer');
		} else {
			print t('Block not found. Unable to edit in composer.');
		}
		?>
		
		</div>
		
		<?
		} ?>
	<? }  ?>
	</fieldset>
	

	</div>
	<div class="ccm-pane-footer">
	<?
	$v = $entry->getVersionObject();
	
	?>
	

	<? if ($entry->isComposerDraft()) { 
	$pp = new Permissions($entry);
	?>
		<? if ($workflow) { ?>
			<?=Loader::helper('concrete/interface')->submit(t('Submit to Workflow'), 'publish', 'right', 'primary')?>
		<? } else { ?>
			<?=Loader::helper('concrete/interface')->submit(t('Publish Page'), 'publish', 'right', 'primary')?>
		<? } ?>
		<? if (PERMISSIONS_MODEL != 'simple' && $pp->canEditPagePermissions()) { ?>
			<?=Loader::helper('concrete/interface')->button_js(t('Permissions'), 'javascript:ccm_composerLaunchPermissions()', 'left', 'primary ccm-composer-hide-on-no-target')?>
		<? } ?>
	<? } else { ?>
		<? if ($workflow) { ?>
			<?=Loader::helper('concrete/interface')->submit(t('Submit to Workflow'), 'publish', 'right', 'primary')?>
		<? } else { ?>
			<?=Loader::helper('concrete/interface')->submit(t('Publish Changes'), 'publish', 'right', 'primary')?>
		<? } ?>
	<? } ?>

	<?=Loader::helper('concrete/interface')->button_js(t('Preview'), 'javascript:ccm_composerLaunchPreview()', 'right', 'ccm-composer-hide-on-approved')?>
	<?=Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'right')?>
	<?=Loader::helper('concrete/interface')->submit(t('Discard'), 'discard', 'left', 'error ccm-composer-hide-on-approved')?>
	
	<?=$form->hidden('entryID', $entry->getCollectionID())?>
	<? if ($entry->isComposerDraft()) { ?>
		<input type="hidden" name="cPublishParentID" value="<?=$entry->getComposerDraftPublishParentID()?>" />
	<? } ?>
	<?=$form->hidden('autosave', 0)?>
	<?=Loader::helper('validation/token')->output('composer')?>
	</div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


	<script type="text/javascript">
	var ccm_composerAutoSaveInterval = false;
	var ccm_composerDoAutoSaveAllowed = true;
	var ccm_composerAddPageTimer = false;

	ccm_updateAddPageHandle = function() {
		clearTimeout(ccm_composerAddPageTimer);
		ccm_composerAddPageTimer = setTimeout(function() {
			var val = $('#ccm-dashboard-composer-form input[name=cName]').val();
			$('#ccm-url-slug-loader').show();
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug', {
				'token': '<?=Loader::helper('validation/token')->generate('get_url_slug')?>',
				'name': val,
				'parentID' : $("input[name=cPublishParentID]").val()
			}, function(r) {
				$('#ccm-url-slug-loader').hide();
				$('#ccm-dashboard-composer-form input[name=cHandle]').val(r);
			});
		}, 150);
	}
	
	ccm_composerDoAutoSave = function(callback) {
		if (!ccm_composerDoAutoSaveAllowed) {
			return false;
		}
		$('#ccm-submit-save').attr('disabled',true);
		$('input[name=autosave]').val('1');
		try {
			tinyMCE.triggerSave(true, true);
		} catch(e) { }
		
		$('#ccm-dashboard-composer-form').ajaxSubmit({
			'dataType': 'json',
			'success': function(r) {
				$('input[name=autosave]').val('0');
				ccm_composerLastSaveTime = new Date();
				$("#composer-save-status").html('<div class="alert alert-info"><?=t("Page saved at ")?>' + r.time + '</div>');
				$(".ccm-composer-hide-on-approved").show();
				$('#ccm-submit-save').attr('disabled',false);
				if (callback) {
					callback();
				}
			}
		});
		
	}
	
	ccm_composerLaunchPreview = function() {
		jQuery.fn.dialog.showLoader();
		<? $t = PageTheme::getSiteTheme(); ?>
		ccm_composerDoAutoSave(function() {
			ccm_previewComposerDraft(<?=$entry->getCollectionID()?>,
				"<?= strlen($entry->getCollectionName())?$entry->getCollectionName():t("New Page")?>");
		});
	}
	
	ccm_composerSelectParentPage = function(cID) {
		$("input[name=cPublishParentID]").val(cID);
		$(".ccm-composer-hide-on-no-target").show();
		$("#ccm-composer-publish-location").load('<?=$this->action("select_publish_target")?>', {'entryID': <?=$entry->getCollectionID()?>, 'cPublishParentID': cID});
		jQuery.fn.dialog.closeTop();

	}	

	ccm_composerSelectParentPageAndSubmit = function(cID) {
		$("input[name=cPublishParentID]").val(cID);
		$(".ccm-composer-hide-on-no-target").show();
		$("#ccm-composer-publish-location").load('<?=$this->action("select_publish_target")?>', {'entryID': <?=$entry->getCollectionID()?>, 'cPublishParentID': cID}, function() {
		 	$("input[name=ccm-publish-draft]").val(1);
		 	$('#ccm-dashboard-composer-form').submit();
		});
	}	
		
	ccm_composerLaunchPermissions = function(cID) {
		var shref = CCM_TOOLS_PATH + '/edit_collection_popup?ctask=edit_permissions&cID=<?=$entry->getCollectionID()?>';
		jQuery.fn.dialog.open({
			title: '<?=t("Permissions")?>',
			href: shref,
			width: '640',
			modal: false,
			height: '310'
		});
	}
	
	ccm_composerEditBlock = function(cID, bID, arHandle, w, h) {
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+cID+'&bID='+bID+'&arHandle=' + encodeURIComponent(arHandle) + '&btask=edit',
			width: w,
			modal: false,
			height: h
		});		
	}
	
	ccm_openComposerPublishTargetWindow = function(submitOnChoose) {
		var shref = CCM_TOOLS_PATH + '/composer_target?cID=<?=$entry->getCollectionID()?>';
		if (submitOnChoose) {
			shref += '&submitOnChoose=1';
		}
		jQuery.fn.dialog.open({
			title: '<?=t("Publish Page")?>',
			href: shref,
			width: '550',
			modal: false,
			height: '400'
		});
	}
	
	$(function() {
		<? if (is_object($v) && $v->isApproved()) { ?>
			$(".ccm-composer-hide-on-approved").hide();
		<? } ?>

		if ($("input[name=cPublishParentID]").val() < 1) {
			$(".ccm-composer-hide-on-no-target").hide();
		}
		
		var ccm_composerAutoSaveIntervalTimeout = 7000;
		var ccm_composerIsPublishClicked = false;
		
		$("#ccm-submit-discard").click(function() {
			return (confirm('<?=t("Discard this draft?")?>'));
		});
		
		$("#ccm-submit-publish").click(function() {
			ccm_composerIsPublishClicked = true;
			$('input[name=ccm-publish-draft]').val(1);
		});
		
		$("#ccm-dashboard-composer-form").submit(function() {
			ccm_composerDoAutoSaveAllowed = false;
		});
		
		<? if ($entry->isComposerDraft()) { ?>
			$("#ccm-dashboard-composer-form").submit(function() {
				if ($("input[name=cPublishParentID]").val() > 0) {
					return true;
				}
				if (ccm_composerIsPublishClicked) {
					ccm_composerIsPublishClicked = false;			
					$('input[name=ccm-publish-draft]').val(0);
	
					<? if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
						ccm_openComposerPublishTargetWindow(true);
						return false;
					<? } else if ($ct->getCollectionTypeComposerPublishMethod() == 'PARENT') { ?>
						return true;				
					<? } else { ?>
						return false;
					<? } ?>
				}
			});
		<? } ?>
		ccm_composerAutoSaveInterval = setInterval(function() {
			ccm_composerDoAutoSave();
		}, 
		ccm_composerAutoSaveIntervalTimeout);
		
	});
	</script>
	
	
<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>
	
	<? if (count($ctArray) > 0) { ?>
	<h3><?=t('What type of page would you like to write?')?></h3>
	<ul class="item-select-list">
	<? foreach($ctArray as $ct) { ?>
		<li class="item-select-page"><a href="<?=$this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?=$ct->getCollectionTypeName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>

	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
<? } ?>


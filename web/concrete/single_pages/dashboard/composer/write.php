<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?

if (isset($entry)) { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(ucfirst($action) . ' ' . $ct->getCollectionTypeName(), false, false, false)?>
	<form method="post" enctype="multipart/form-data" action="<?=$this->action('save')?>" id="ccm-dashboard-composer-form">
	<div class="ccm-pane-body">
	

	<div id="composer-save-status"></div>
	
	<fieldset>
	<legend><?=t("Basic Information")?></legend>
	<div class="clearfix">
		<?=$form->label('cName', t('Name'))?>
		<div class="input"><?=$form->text('cName', Loader::helper("text")->entities($name), array('class' => 'span12'))?></div>		
	</div>

	<div class="clearfix">
		<?=$form->label('cDescription', t('Short Description'))?>
		<div class="input"><?=$form->textarea('cDescription', Loader::helper("text")->entities($description), array('class' => 'span12', 'rows' => 5))?></div>		
	</div>

	<div class="clearfix">
		<?=$form->label('cDatePublic', t('Date Posted'))?>
		<div class="input"><? 
		if ($this->controller->isPost()) { 	
			$cDatePublic = Loader::helper('form/date_time')->translate('cDatePublic');
		}
		?><?=Loader::helper('form/date_time')->datetime('cDatePublic', $cDatePublic)?></div>		
	</div>
	
	</fieldset>
	
	<? if ($entry->isComposerDraft()) { ?>
	<fieldset>
	<legend><?=t('Publish Location')?></legend>
	<div class="clearfix">
		<label></label>
		<div class="input">
		<span id="ccm-composer-publish-location"><?
		if ($entry->getComposerDraftPublishParentID() > 0) { 
			print $this->controller->getComposerDraftPublishText($entry);
		} ?>
		</span>
		
		<? 
	
	if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
		
		<a href="javascript:void(0)" onclick="ccm_openComposerPublishTargetWindow(false)"><?=t('Choose publish location.')?></a>
	
	<? } 
	
	?></div></div>
	</fieldset>
	<? } ?>
	
	<fieldset>
	<legend><?=t('Attributes &amp; Content')?></legend>
	<? 
	foreach($contentitems as $ci) {
		if ($ci instanceof AttributeKey) { 
			$ak = $ci;
			if (is_object($entry)) {
				$value = $entry->getAttributeValueObject($ak);
			}
			?>
			<div class="clearfix">
				<?=$ak->render('label');?>
				<div class="input">
					<?=$ak->render('composer', $value, true)?>
				</div>
			</div>
		
		<? } else { 
			$b = $ci; 
			$b = $entry->getComposerBlockInstance($b);
			?>
		
		<div class="clearfix">
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
	
	

	</div>
	<div class="ccm-pane-footer">
	<?
	$v = $entry->getVersionObject();
	
	?>
	

	<? if ($entry->isComposerDraft()) { 
	$pp = new Permissions($entry);
	?>
		<?=Loader::helper('concrete/interface')->submit(t('Publish Page'), 'publish', 'right', 'primary')?>
		<? if (PERMISSIONS_MODEL != 'simple' && $pp->canAdmin()) { ?>
			<?=Loader::helper('concrete/interface')->button_js(t('Permissions'), 'javascript:ccm_composerLaunchPermissions()', 'left', 'primary ccm-composer-hide-on-no-target')?>
		<? } ?>
	<? } else { ?>
		<?=Loader::helper('concrete/interface')->submit(t('Publish Changes'), 'publish', 'right', 'primary')?>
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
	
	ccm_composerDoAutoSave = function(callback) {
		if (!ccm_composerDoAutoSaveAllowed) {
			return false;
		}
		
		$('input[name=autosave]').val('1');
		try {
			tinyMCE.triggerSave(true, true);
		} catch(e) { }
		
		$('#ccm-dashboard-composer-form').ajaxSubmit({
			'dataType': 'json',
			'success': function(r) {
				$('input[name=autosave]').val('0');
				ccm_composerLastSaveTime = new Date();
				$("#composer-save-status").html('<div class="block-message alert-message info"><p><?=t("Page saved at ")?>' + r.time + '</p></div>');
				$(".ccm-composer-hide-on-approved").show();
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
			ccm_previewInternalTheme(<?=$entry->getCollectionID()?>, <?=$t->getThemeID()?>, '<?=addslashes(str_replace(array("\r","\n","\n"),'',$t->getThemeName()))?>');
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
		 	$("input[name=ccm-submit-publish]").click();
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

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span14 offset1')?>
	
	<? if (count($ctArray) > 0) { ?>
	<h3><?=t('What type of page would you like to write?')?></h3>
	<ul class="icon-select-list">
	<? foreach($ctArray as $ct) { ?>
		<li class="icon-select-page"><a href="<?=$this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?=$ct->getCollectionTypeName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>

	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
<? } ?>


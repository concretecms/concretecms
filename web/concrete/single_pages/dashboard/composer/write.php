<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
if (isset($entry)) { ?>

	<form method="post" enctype="multipart/form-data" action="<?=$this->action('save')?>" id="ccm-dashboard-composer-form">
	
	<h1><span><?=ucfirst($action)?> <?=$ct->getCollectionTypeName()?></span></h1>
	<div class="ccm-dashboard-inner" id="ccm-dashboard-composer">
	<div id="composer-save-status"></div>
	<h2><?=t("Basic Information")?></h2>
	<ol>
		<li>
		<strong><?=$form->label('cName', t('Name'))?></strong><br/>
		<?=$form->text('cName', $name)?>		
		</li>
		<li>
		<strong><?=$form->label('cDescription', t('Short Description'))?></strong><br/>
		<?=$form->textarea('cDescription', $description)?>		
		</li>
		<li>
		<strong><?=$form->label('cDatePublic', t('Date Posted'))?></strong><br/>
		<? 
		if ($this->controller->isPost()) { 	
			$cDatePublic = Loader::helper('form/date_time')->translate('cDatePublic');
		}
		?>		
		<?=Loader::helper('form/date_time')->datetime('cDatePublic', $cDatePublic)?>		
		</li>
	</ol>

	<? if ($entry->isComposerDraft()) { ?>
	<h2><?=t('Publish Location')?></h2>
	<ol><li><span id="ccm-composer-publish-location"><?
		if ($entry->getComposerDraftPublishParentID() > 0) { 
			print $this->controller->getComposerDraftPublishText($entry);
		} ?>
		</span>
		
		<? 
	
	if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
		
		<a href="javascript:void(0)" onclick="ccm_openComposerPublishTargetWindow(false)"><?=t('Choose publish location.')?></a>
	
	<? } 
	
	?></li></ol>
	<? } ?>
	

	<h2><?=t('Attributes &amp; Content')?></h2>
	
	<ol>
	<? 
	foreach($contentitems as $ci) {
		if ($ci instanceof AttributeKey) { 
			$ak = $ci;
			if (is_object($entry)) {
				$value = $entry->getAttributeValueObject($ak);
			}
			?>
			<li><strong><?=$ak->render('label');?></strong><br/>
			<?=$ak->render('form', $value, true)?>	
			</li>
		
		<? } else { 
			$b = $ci; 
			$b = $entry->getComposerBlockInstance($b);
			?>
		
		<li>
		<?
		if (is_object($b)) {
			$bv = new BlockView();
			$bv->render($b, 'composer');
		} else {
			print t('Block not found. Unable to edit in composer.');
		}
		?>
		
		</li>
		
		<?
		} ?>
	<? }  ?>
	</ol>
	
	
		<?
		$v = $entry->getVersionObject();
		
		?>
		
		<?=Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left')?>
		<?=Loader::helper('concrete/interface')->submit(t('Discard'), 'discard', 'left', 'ccm-composer-hide-on-approved')?>
		<?=Loader::helper('concrete/interface')->button_js(t('Preview'), 'javascript:ccm_composerLaunchPreview()', 'left', 'ccm-composer-hide-on-approved')?>

		<? if ($entry->isComposerDraft()) { 
		$pp = new Permissions($entry);
		?>
			<? if (PERMISSIONS_MODEL != 'simple' && $pp->canAdmin()) { ?>
				<?=Loader::helper('concrete/interface')->button_js(t('Permissions'), 'javascript:ccm_composerLaunchPermissions()', 'left', 'ccm-composer-hide-on-no-target')?>
			<? } ?>
			<?=Loader::helper('concrete/interface')->submit(t('Publish Page'), 'publish')?>
		<? } else { ?>
			<?=Loader::helper('concrete/interface')->submit(t('Publish Changes'), 'publish')?>
		<? } ?>
		
		<?=$form->hidden('entryID', $entry->getCollectionID())?>
		<? if ($entry->isComposerDraft()) { ?>
			<input type="hidden" name="cPublishParentID" value="<?=$entry->getComposerDraftPublishParentID()?>" />
		<? } ?>
		<?=$form->hidden('autosave', 0)?>
		<?=Loader::helper('validation/token')->output('composer')?>
		<div class="ccm-spacer">&nbsp;</div>
		
	</div>
	</form>

	<script type="text/javascript">
	var ccm_composerAutoSaveInterval = false;
	
	ccm_composerDoAutoSave = function() {
		$('input[name=autosave]').val('1');
		try {
			tinyMCE.triggerSave(true, true);
		} catch(e) { }
		
		$('#ccm-dashboard-composer-form').ajaxSubmit({
			'dataType': 'json',
			'success': function(r) {
				$('input[name=autosave]').val('0');
				ccm_composerLastSaveTime = new Date();
				$("#composer-save-status").html('<?=t("Page saved at ")?>' + r.time);
				$(".ccm-composer-hide-on-approved").show();
			}
		});
	}
	
	ccm_composerLaunchPreview = function() {
		<? $t = PageTheme::getSiteTheme(); ?>
		ccm_previewInternalTheme(<?=$entry->getCollectionID()?>, <?=$t->getThemeID()?>, '<?=addslashes(str_replace(array("\r","\n","\n"),'',$t->getThemeName()))?>');
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
		var shref = CCM_TOOLS_PATH + '/edit_collection_popup?ctask=edit_permissions_composer&cID=<?=$entry->getCollectionID()?>';
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

	<h1><span><?=t('Composer')?></span></h1>
	<div class="ccm-dashboard-inner" id="ccm-dashboard-composer">


	<? if (count($ctArray) > 0) { ?>
	<h2><?=t('What type of page would you like to write?')?></h2>
	<ul>
	<? foreach($ctArray as $ct) { ?>
		<li><a href="<?=$this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?=$ct->getCollectionTypeName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>

	</div>
	
<? } ?>


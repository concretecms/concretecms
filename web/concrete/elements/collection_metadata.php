<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c;
Loader::model('collection_types');
Loader::model('collection_attributes');
$dt = Loader::helper('form/date_time');
$uh = Loader::helper('form/user_selector');

if ($cp->canAdminPage()) {
	$ctArray = CollectionType::getList();
}
?>
<div class="ccm-pane-controls ccm-ui">
<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

	<script type="text/javascript"> 
		
		function ccm_triggerSelectUser(uID, uName) {
			$('#ccm-uID').val(uID);
			$('#ccm-uName').html(uName);
		}
		
		
		var ccm_activePropertiesTab = "ccm-properties-standard";
		
		$("#ccm-properties-tabs a").click(function() {
			$("li.active").removeClass('active');
			$("#" + ccm_activePropertiesTab + "-tab").hide();
			ccm_activePropertiesTab = $(this).attr('id');
			$(this).parent().addClass("active");
			$("#" + ccm_activePropertiesTab + "-tab").show();
			
			if (ccm_activePropertiesTab == 'ccm-properties-custom') {
				$('#ccm-dialog-content1').dialog('option','height','540');
			} else {
				$('#ccm-dialog-content1').dialog('option','height','460');
			}
			$('#ccm-dialog-content1').dialog('option','position','center');

		});
		
		ccm_settingsSetupCacheForm = function() {
			var obj = $('input[name=cCacheFullPageContent]:checked');
			if (obj.attr('enable-cache') == 1) {
				$('div.ccm-properties-cache-lifetime input').attr('disabled', false);
			} else {
				$('div.ccm-properties-cache-lifetime input').attr('disabled', true);
				$('input[name=cCacheFullPageContentOverrideLifetime][value=0]').attr('checked', true);
			}

			var obj2 = $('input[name=cCacheFullPageContentOverrideLifetime]:checked');
			if (obj2.val() == 'custom') {
				$('input[name=cCacheFullPageContentLifetimeCustom]').attr('disabled', false);
			} else {
				$('input[name=cCacheFullPageContentLifetimeCustom]').attr('disabled', true);
				$('input[name=cCacheFullPageContentLifetimeCustom]').val('');
			}

		}
		
		$(function() {
			$("input[name=cCacheFullPageContent]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=cCacheFullPageContentOverrideLifetime]").click(function() {
				ccm_settingsSetupCacheForm();
			});
			$("input[name=cCacheFullPageContentOverrideLifetime][value=custom]").click(function() {
				$('input[name=cCacheFullPageContentLifetimeCustom]').get(0).focus();
			});
			ccm_settingsSetupCacheForm();
			$("#ccmMetadataForm").ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					jQuery.fn.dialog.showLoader();
				},
				success: function(r) {
					try {
						var r = eval('(' + r + ')');
						if (r != null && r.rel == 'SITEMAP') {
							jQuery.fn.dialog.hideLoader();
							jQuery.fn.dialog.closeTop();
							ccmSitemapHighlightPageLabel(r.cID, r.name);
						} else {
							ccm_mainNavDisableDirectExit();
							ccm_hidePane(function() {
								jQuery.fn.dialog.hideLoader();						
							});
						}
						ccmAlert.hud(ccmi18n.savePropertiesMsg, 2000, 'success', ccmi18n.properties);
					} catch(e) {
						alert(r);
					}
				}
			});
		});
	</script>
	

	<div id="ccm-required-meta">
	
		
	<ul class="tabs" id="ccm-properties-tabs">
		<li <? if (!$c->isMasterCollection()) { ?>class="active"<? } else { ?>style="display: none"<? } ?>><a href="javascript:void(0)" id="ccm-properties-standard"><?=t('Standard Properties')?></a></li>
		<li <? if ($c->isMasterCollection()) { ?>style="display: none"<? } ?>><a href="javascript:void(0)" id="ccm-page-paths"><?=t('Page Paths and Location')?></a></li>
		<li <? if ($c->isMasterCollection()) { ?>class="active"<? } ?>><a href="javascript:void(0)" id="ccm-properties-cache"><?=t('Speed Settings')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-properties-custom"><?=t('Custom Attributes')?></a></li>
	</ul>

	<div id="ccm-properties-standard-tab">
	
	<div class="clearfix">
		<label for="cName"><?=t('Name')?></label>
		<div class="input"><input type="text" id="cName" name="cName" value="<?=htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" /></div>
	</div>

	<div class="clearfix">
		<label for="cDatePublic"><?=t('Public Date/Time')?></label>
		<div class="input"><? print $dt->datetime('cDatePublic', $c->getCollectionDatePublic(null, 'user')); ?></div>
	</div>


	<div class="clearfix">
	<label><?=t('Owner')?></label>
	<div class="input">
	
		<? 
		print $uh->selectUser('uID', $c->getCollectionUserID());
		?>
	</div>
	</div>
		
	
	<div class="clearfix">
	<label for="cDescription"><?=t('Description')?></label>
	<div class="input"><textarea id="cDescription" name="cDescription" class="ccm-input-text" style="width: 500px; height: 50px"><?=$c->getCollectionDescription()?></textarea></div>
	</div>
	
	</div>
	
	<div id="ccm-page-paths-tab" style="display: none">
		
		<div class="clearfix">
		<label for="cHandle"><?= t('Canonical URL')?></label>
		<div class="input">
		<?php if (!$c->isGeneratedCollection()) { ?>
			<?=BASE_URL . DIR_REL;?><? if (URL_REWRITING == false) { ?>/<?=DISPATCHER_FILENAME?><? } ?><?
			$cPath = substr($c->getCollectionPath(), strrpos($c->getCollectionPath(), '/') + 1);
			print substr($c->getCollectionPath(), 0, strrpos($c->getCollectionPath(), '/'))?>/<input type="text" name="cHandle" value="<?php echo $cPath?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?php echo $c->getCollectionHandle()?>"><br /><br />
		<?php  } else { ?>
			<?php echo $c->getCollectionHandle()?><br /><br />
		<?php  } ?>
			<span class="help-block"><?=t('This page must always be available from at least one URL. That URL is listed above.')?></span>
		</div>
		</div>
		
		<?php if (!$c->isGeneratedCollection()) { ?>
		<div class="clearfix" id="ccm-more-page-paths">
			<label><?= t('More URLs') ?></label>
	
			<?php
				$paths = $c->getPagePaths();
				foreach ($paths as $path) {
					if (!$path['ppIsCanonical']) {
						$ppID = $path['ppID'];
						$cPath = $path['cPath'];
						echo '<div class="input ccm-meta_path">' .
			     			'<input type="text" name="ppURL-' . $ppID . '" class="ccm-input-text" value="' . $cPath . '" id="ppID-'. $ppID . '"> ' .
			     			'<a href="javascript:void(0)" class="ccm-meta-path-del">' . t('Remove Path') . '</a></div>'."\n";
					}
				}
			?>
		    <div class="input">
	     		<input type="text" name="ppURL-add-0" class="ccm-input-text" value="" id="ppID-add-0">
		 		<a href="javascript:void(0)" class="ccm-meta-path-add"><?=t('Add Path')?></a>
			</div>
		</div>
		<?php } ?>
	
	</div>
	
	<style type="text/css">
	#ccm-more-page-paths div.input {margin-bottom: 10px;}
	</style>
	
	<div id="ccm-properties-custom-tab" style="display: none">
		<? Loader::element('collection_metadata_fields', array('c'=>$c ) ); ?>
	</div>

	<div id="ccm-properties-cache-tab" style="display: none" class="form-stacked">
		
		<? if (!ENABLE_CACHE) {
			print t('The cache has been disabled. Full page caching is not available.');
		} else { ?>
			<? $form = Loader::helper('form');?>
			<?
			switch(FULL_PAGE_CACHE_GLOBAL) {
				case 'blocks':
					$globalSetting = t('cache page if all blocks support it.');
					$enableCache = 1;
					break;
				case 'all':
					$globalSetting = t('enable full page cache.');
					$enableCache = 1;
					break;
				case 0:
					$globalSetting = t('disable full page cache.');
					$enableCache = 0;
					break;
			}
			switch(FULL_PAGE_CACHE_LIFETIME) {
				case 'default':
					$globalSettingLifetime = t('%s minutes', CACHE_LIFETIME / 60);
					break;
				case 'custom':
					$custom = Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM');
					$globalSettingLifetime = t('%s minutes', $custom);
					break;
				case 'forever':
					$globalSettingLifetime = t('Until manually cleared');
					break;
			}
			?>

			<div class="clearfix">
			<label><?=t('Full Page Caching')?></label>

			<div class="input">
			<ul class="inputs-list">
			<li><label><?=$form->radio('cCacheFullPageContent', -1, $c->getCollectionFullPageCaching(), array('enable-cache' => $enableCache))?>
			<span><?=t('Use global setting - %s', $globalSetting)?></span>
			</label></li>
			<li><label><?=$form->radio('cCacheFullPageContent', 0, $c->getCollectionFullPageCaching(), array('enable-cache' => 0))?>
			<span><?=t('Do not cache this page.')?></span>
			</label></li>
			<li><label><?=$form->radio('cCacheFullPageContent', 1, $c->getCollectionFullPageCaching(), array('enable-cache' => 1))?>
			<span><?=t('Cache this page.')?></span>
			</label>
			</li>
			</ul>
			</div>
			
			</div>
			
			<div class="clearfix">
			<label><?=t('Cache for how long?')?></label>
			
			<div class="ccm-properties-cache-lifetime input">
			<ul class="inputs-list">
				<? $val = ($c->getCollectionFullPageCachingLifetimeCustomValue() > 0 && $c->getCollectionFullPageCachingLifetime()) ? $c->getCollectionFullPageCachingLifetimeCustomValue() : ''; ?>
				<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 0, $c->getCollectionFullPageCachingLifetime())?> 
				<?=t('Use global setting - %s', $globalSettingLifetime)?>
				</span></label></li>
				<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'default', $c->getCollectionFullPageCachingLifetime())?> 
				<?=t('Default - %s minutes', CACHE_LIFETIME / 60)?>
				</span></label></li>
				<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'forever', $c->getCollectionFullPageCachingLifetime())?>
				<?=t('Until manually cleared')?>
				</span></label></li>
				<li><label><span><?=$form->radio('cCacheFullPageContentOverrideLifetime', 'custom', $c->getCollectionFullPageCachingLifetime())?>
				<?=t('Custom')?>
				</span></label>
				<div style="margin-top: 4px; margin-left: 16px">
					<?=$form->text('cCacheFullPageContentLifetimeCustom', $val, array('style' => 'width: 40px'))?> <?=t('minutes')?>	
				</div>
				</li>
			</ul>
			</div>
		<? } ?>
	</div>
	
	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="dialog-buttons">
	<a href="javascript:void(0)" class="btn primary" onclick="$('#ccmMetadataForm').submit()"><?=t('Save')?></a>
	</div>

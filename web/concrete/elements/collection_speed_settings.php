<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c;

?>

<div class="ccm-ui">

<form method="post" id="ccmSpeedSettingsForm" action="<?=$c->getCollectionAction()?>">

	<script type="text/javascript"> 
		
		ccm_settingsSetupCacheForm = function(reset) {
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
				if (reset) {
					$('input[name=cCacheFullPageContentLifetimeCustom]').val('');
				}
			}

		}
		
		$(function() {
			$("input[name=cCacheFullPageContent]").click(function() {
				ccm_settingsSetupCacheForm(true);
			});
			$("input[name=cCacheFullPageContentOverrideLifetime]").click(function() {
				ccm_settingsSetupCacheForm(true);
			});
			$("input[name=cCacheFullPageContentOverrideLifetime][value=custom]").click(function() {
				$('input[name=cCacheFullPageContentLifetimeCustom]').get(0).focus();
			});
			ccm_settingsSetupCacheForm();
			$("#ccmSpeedSettingsForm").ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					jQuery.fn.dialog.showLoader();
				},
				success: function(r) {
					try {
						var r = eval('(' + r + ')');
						jQuery.fn.dialog.hideLoader();
						jQuery.fn.dialog.closeTop();
						if (r != null && r.rel == 'SITEMAP') {
							ccmSitemapHighlightPageLabel(r.cID, r.name);
						}
						ccmAlert.hud(ccmi18n.saveSpeedSettingsMsg, 2000, 'success', ccmi18n.properties);
					} catch(e) {
						alert(r);
					}
				}
			});
		});
	</script>
	


	<div id="ccm-properties-cache-tab">
		
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
				<li><label><span><input type="radio" name="cCacheFullPageContentOverrideLifetime" value="0" <? if ($c->getCollectionFullPageCachingLifetime() == '0') { ?> checked="checked" <? } ?> /> 
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
					<label><?=$form->text('cCacheFullPageContentLifetimeCustom', $val, array('style' => 'width: 40px'))?> <?=t('minutes')?></label>
				</div>
				</li>
			</ul>
			</div>
		<? } ?>
	</div>	
	
	<input type="hidden" name="update_speed_settings" value="1" />
	<input type="hidden" name="processCollection" value="1">
</form>
</div>

	<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn primary ccm-button-right" onclick="$('#ccmSpeedSettingsForm').submit()"><?=t('Save')?></a>
	</div>

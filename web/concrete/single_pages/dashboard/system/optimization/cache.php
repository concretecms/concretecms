<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->url('/dashboard/system/optimization/cache', 'update_cache')?>">
    <?=$this->controller->token->output('update_cache')?>
        
    <fieldset style="margin-bottom: 15px">
    <legend style="display: inline-block; margin-bottom: 0; width: auto; font-size: 14px; font-weight: bold" class="launch-tooltip" data-placement="right" title="<?=t('Stores the output of blocks which support block caching')?>"><?=t('Block Cache')?></legend>

    <div class="radio">
        <label>
            <input type="radio" name="ENABLE_BLOCK_CACHE" value="0" <?php if (ENABLE_BLOCK_CACHE == false) { ?> checked <?php  } ?> />
            <?=t('Off - Good for development of custom blocks.')?>
        </label>
    </div>

    <div class="radio">
        <label>
            <input type="radio" name="ENABLE_BLOCK_CACHE" value="1" <?php if (ENABLE_BLOCK_CACHE == true) { ?> checked <?php  } ?> />
            <?=t('On - Helps speed up a live site.')?>
        </label>
    </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px">
        <legend style="display: inline-block; margin-bottom: 0; width: auto; font-size: 14px; font-weight: bold" class="launch-tooltip" data-placement="right" title="<?=t('Stores the generation of CSS and JavaScript assets')?>"><?=t('CSS and JavaScript Cache')?></legend>

        <div class="radio">
            <label>
                <input type="radio" name="ENABLE_ASSET_CACHE" value="0" <?php if (ENABLE_ASSET_CACHE == false) { ?> checked <?php  } ?> />
                <span><?=t('Off - Good for active theme and block development.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="ENABLE_ASSET_CACHE" value="1" <?php  if (ENABLE_ASSET_CACHE == true) { ?> checked <?php  } ?> />
                <span><?=t('On - Helps speed up a live site.')?></span>
            </label>
        </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px">
        <legend style="display: inline-block; margin-bottom: 0; width: auto; font-size: 14px; font-weight: bold" class="launch-tooltip" data-placement="right" title="<?=t('Stores the location and existence of source code files')?>"><?=t('Overrides Cache')?></legend>

        <div class="radio">
            <label>
                <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="0" <?php  if (ENABLE_OVERRIDE_CACHE == false) { ?> checked <?php  } ?> />
                <span><?=t('Off - Good for development.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="1" <?php  if (ENABLE_OVERRIDE_CACHE == true) { ?> checked <?php  } ?> />
                <span><?=t('On - Helps speed up a live site.')?></span>
            </label>
        </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px">
        <legend style="display: inline-block; margin-bottom: 0; width: auto; font-size: 14px; font-weight: bold" class="launch-tooltip" data-placement="right" title="<?=t('Stores the output of an entire page')?>"><?=t('Full Page Caching')?></legend>
        <div class="radio">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="0" <?php  if (FULL_PAGE_CACHE_GLOBAL == 0) { ?> checked <?php  } ?> />
                <span><?=t('Off - Turn it on by hand for specific pages.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="blocks" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'blocks') { ?> checked <?php  } ?> />
                <span><?=t('On - If blocks on the particular page allow it.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="all" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'all') { ?> checked <?php  } ?> />
                <span><?=t('On - In all cases.')?></span>
            </label>
        </div>    
    </fieldset>

    <fieldset>
        <legend style="display: inline-block; margin-bottom: 0; width: auto; font-size: 14px; font-weight: bold" class="launch-tooltip" data-placement="right" title="<?=t('Sets the amount of time to store the page output before generating a new version')?>"><?=t('Expire Pages from Cache')?></legend>

        <div class="radio">
          <label>
              <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="default" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'default') { ?> checked <?php  } ?> />
              <span><?=t('Every %s (default setting).', Loader::helper('date')->describeInterval(CACHE_LIFETIME))?></span>
          </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="forever" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'forever') { ?> checked <?php  } ?> />
                <span><?=t('Only when manually removed or the cache is cleared.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="custom" style="margin-bottom:1px; vertical-align:text-bottom;" <?php if (FULL_PAGE_CACHE_LIFETIME == 'custom') { ?> checked <?php  } ?> />
                <span>
                    <?=t('Every ')?>
                    <input type="text" name="FULL_PAGE_CACHE_LIFETIME_CUSTOM" value="<?= h(Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM')) ?>" size="4" />
                    <?=t(' minutes.')?>
                </span>
            </label>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
        </div>
    </div>

</form>

<script type="text/javascript">
	ccm_settingsSetupCacheForm = function() {
		var obj = $('input[name=ENABLE_CACHE]:checked');
		if (obj.val() == 0) {
			$('div.ccm-cache-settings-full-page input').attr('disabled', true);
			$('input[name=FULL_PAGE_CACHE_LIFETIME][value=default]').attr('checked', true);
		} else {
			$('div.ccm-cache-settings-full-page input').attr('disabled', false);
		}
		var obj2 = $('input[name=FULL_PAGE_CACHE_LIFETIME]:checked');
		if (obj2.val() == 'custom') {
			$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').attr('disabled', false);
		} else {
			$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').attr('disabled', true);
			$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').val('');
		}			
	}
	
	$(function(){ 
		$("input[name='CONTENTS_TXT_EDITOR_MODE']").each(function(i,el){ 
			el.onchange=function(){isTxtEditorModeCustom();}
		})	 	
		$("input[name=ENABLE_CACHE]").click(function() {
			ccm_settingsSetupCacheForm();
		});
		$("input[name=FULL_PAGE_CACHE_LIFETIME]").click(function() {
			ccm_settingsSetupCacheForm();
		});
		$("input[name=FULL_PAGE_CACHE_LIFETIME][value=custom]").click(function() {
			$('input[name=FULL_PAGE_CACHE_LIFETIME_CUSTOM]').get(0).focus();
		});
		ccm_settingsSetupCacheForm();
	});
	function isTxtEditorModeCustom(){
		if($("input[name='CONTENTS_TXT_EDITOR_MODE']:checked").val()=='CUSTOM'){
			$('#cstmEditorTxtAreaWrap').css('display','block');
		}else{
			$('#cstmEditorTxtAreaWrap').css('display','none');
		}
	}
</script>

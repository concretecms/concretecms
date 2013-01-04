<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cache &amp; Speed Settings'), false, 'span10 offset1', false)?>

<form method="post" class="form-horizontal" id="update-cache-form" action="<?php echo $this->url('/dashboard/system/optimization/cache', 'update_cache')?>">
    <div class="ccm-pane-body">
        <?php echo $this->controller->token->output('update_cache')?>
        
        <fieldset>
            <legend style="margin-bottom: 0px"><?php echo t('Block Cache')?></legend>
        	<div class="control-group">
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="ENABLE_BLOCK_CACHE" value="0" <?php  if (ENABLE_BLOCK_CACHE == false) { ?> checked <?php  } ?> />
                    <span><?php echo t('Off - Good for development of custom blocks.')?></span>
                </label>
                </div>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="ENABLE_BLOCK_CACHE" value="1" <?php  if (ENABLE_BLOCK_CACHE == true) { ?> checked <?php  } ?> />
                    <span><?php echo t('On - Helps speed up a live site.')?></span>
                </label>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend style="margin-bottom: 0px"><?php echo t('Overrides Cache')?></legend>
            <div class="control-group">
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="0" <?php  if (ENABLE_OVERRIDE_CACHE == false) { ?> checked <?php  } ?> />
                    <span><?php echo t('Off - Good for development.')?></span>
                </label>
                </div>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="1" <?php  if (ENABLE_OVERRIDE_CACHE == true) { ?> checked <?php  } ?> />
                    <span><?php echo t('On - Helps speed up a live site.')?></span>
                </label>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend style="margin-bottom: 0px"><?php echo t('Full Page Caching')?></legend>
            <div class="control-group">
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="0" <?php  if (FULL_PAGE_CACHE_GLOBAL == 0) { ?> checked <?php  } ?> />
                    <span><?php echo t('Off - Turn it on by hand for specific pages.')?></span>
                </label>
                </div>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="blocks" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'blocks') { ?> checked <?php  } ?> />
                    <span><?php echo t('On - If blocks on the particular page allow it.')?></span>
                </label>
                </div>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="all" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'all') { ?> checked <?php  } ?> />
                    <span><?php echo t('On - In all cases.')?></span>
                </label>
                </div>
            </div>    

            <div class="control-group">
                <label class="control-label"><?=t('Expire Pages from Cache')?></label>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="default" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'default') { ?> checked <?php  } ?> />
                    <span><?php echo t('Every %s (default setting).', Loader::helper('date')->timeSince(time()-CACHE_LIFETIME))?></span>
                </label>
                </div>
                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="forever" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'forever') { ?> checked <?php  } ?> />
                    <span><?php echo t('Only when manually removed or the cache is cleared.')?></span>
                </label>
                </div>

                <div class="controls">
                <label class="radio">
                    <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="custom" style="margin-bottom:1px; vertical-align:text-bottom;" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'custom') { ?> checked <?php  } ?> />
                    <span>
                        <?php echo t('Every ')?>
                        <?php echo $form->text('FULL_PAGE_CACHE_LIFETIME_CUSTOM', Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM'), array('style' => 'width: 40px;text-align:center;'))?>
                        <?php echo t(' minutes.')?>
                    </span>
                </label>
                </div>
            </div>    


        </fieldset>
    </div>
    <div class="ccm-pane-footer">
        <?
        print $interface->submit(t('Save'), 'update-cache-form', 'right', 'primary');
        ?>    
    </div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>  

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
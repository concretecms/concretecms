<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cache &amp; Speed Settings'), false, 'span10 offset1', false)?>

<form method="post" id="update-cache-form" action="<?php echo $this->url('/dashboard/system/optimization/cache', 'update_cache')?>">
    <div class="ccm-pane-body">
        <?php echo $this->controller->token->output('update_cache')?>
    
        <h3><?php echo t('Basic Cache')?></h3>
    	<div class="clearfix inputs-list">
            <label>
                <input type="radio" name="ENABLE_CACHE" value="0" <?php  if (ENABLE_CACHE == false) { ?> checked <?php  } ?> />
                <span><?php echo t('Off - Good for development.')?></span>
            </label>
            <label>
                <input type="radio" name="ENABLE_CACHE" value="1" <?php  if (ENABLE_CACHE == true) { ?> checked <?php  } ?> />
                <span><?php echo t('On - Helps speed up a live site.')?></span>
            </label>
        </div>

        <h3><?php echo t('Overrides Cache')?></h3>
    	<div class="clearfix inputs-list">
            <label>
                <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="0" <?php  if (ENABLE_OVERRIDE_CACHE == false) { ?> checked <?php  } ?> />
                <span><?php echo t('Off - Good for development.')?></span>
            </label>
            <label>
                <input type="radio" name="ENABLE_OVERRIDE_CACHE" value="1" <?php  if (ENABLE_OVERRIDE_CACHE == true) { ?> checked <?php  } ?> />
                <span><?php echo t('On - Helps speed up a live site.')?></span>
            </label>
        </div>
        
        <h3><?php echo t('Full Page Caching')?></h3>
    	<div class="clearfix inputs-list">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="0" <?php  if (FULL_PAGE_CACHE_GLOBAL == 0) { ?> checked <?php  } ?> />
                <span><?php echo t('Off - Turn it on by hand for specific pages.')?></span>
            </label>
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="blocks" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'blocks') { ?> checked <?php  } ?> />
                <span><?php echo t('On - If blocks allow it.')?></span>
            </label>
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="all" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'all') { ?> checked <?php  } ?> />
                <span><?php echo t('On - In all cases.')?></span>
            </label>
        </div>
        
        <h3><?php echo t('Full Page Cache Rebuild')?></h3>
    	<div class="clearfix inputs-list ccm-cache-settings-full-page">
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="default" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'default') { ?> checked <?php  } ?> />
                <span><?php echo t('Automatic.')?></span>
            </label>
            <label style="line-height:36px;">
                <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="custom" style="margin-bottom:1px; vertical-align:text-bottom;" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'custom') { ?> checked <?php  } ?> />
                <span>
					<?php echo t('Every ')?>
					<?php echo $form->text('FULL_PAGE_CACHE_LIFETIME_CUSTOM', Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM'), array('style' => 'width: 40px;text-align:center;'))?>
					<?php echo t(' minutes.')?>
				</span>
            </label>
            <label>
                <input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="forever" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'forever') { ?> checked <?php  } ?> />
                <span><?php echo t('Manually cleared.')?></span>
            </label>
        </div>

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
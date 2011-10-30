<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Speed Settings'), false, 'span12 offset2', false)?>

<form method="post" id="update-cache-form" action="<?php echo $this->url('/dashboard/system/maintenance/cache', 'update_cache')?>">
    <div class="ccm-pane-body">
        <?php echo $this->controller->token->output('update_cache')?>
            
        <p><?php echo t('Caching stores frequently accessed data so it can be more quickly retrieved. Full page caching can dramatically lighten the load on your server and speed up your website, and is highly recommended in high traffic situations.')?></p>
    
        <h3><?php echo t('Basic Cache')?></h3>
    
        <div class="ccm-dashboard-radio"><input type="radio" name="ENABLE_CACHE" value="0" <?php  if (ENABLE_CACHE == false) { ?> checked <?php  } ?> /> <?php echo t('Disabled')?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="ENABLE_CACHE" value="1" <?php  if (ENABLE_CACHE == true) { ?> checked <?php  } ?> /> <?php echo t('Enabled')?>
        </div>
        <div class="ccm-cache-settings-full-page">
    
        <h3><?php echo t('Full Page Caching')?></h3>
        
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="0" <?php  if (FULL_PAGE_CACHE_GLOBAL == 0) { ?> checked <?php  } ?> /> <?php echo t('Disabled, unless specified at page level.')?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="blocks" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'blocks') { ?> checked <?php  } ?> /> <?php echo t('Enabled if blocks allow it, unless specified at page level.')?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_GLOBAL" value="all" <?php  if (FULL_PAGE_CACHE_GLOBAL == 'all') { ?> checked <?php  } ?> /> <?php echo t('Enabled in all cases (Emergency Cache Mode)')?></div>
        
        
        <h5><?php echo t('Full Page Cache Lifetime')?></h5>
        
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="default" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'default') { ?> checked <?php  } ?> /> <?php echo t('Default - %s minutes', CACHE_LIFETIME / 60)?></div>
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="custom" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'custom') { ?> checked <?php  } ?> /> <?php echo t('Custom - ')?>
            <?php echo $form->text('FULL_PAGE_CACHE_LIFETIME_CUSTOM', Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM'), array('style' => 'width: 40px'))?> <?php echo t('minutes')?>		
        </div>
        <div class="ccm-dashboard-radio"><input type="radio" name="FULL_PAGE_CACHE_LIFETIME" value="forever" <?php  if (FULL_PAGE_CACHE_LIFETIME == 'forever') { ?> checked <?php  } ?> /> <?php echo t('Until manually cleared')?></div>
        
        </div>
    </div>
    <div class="ccm-pane-footer">
        <?
        print $interface->submit(t('Update Cache'), 'update-cache-form', 'left','primary');
        ?>
    
    </div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>  

<br/>  
    
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Clear Cache'), false, 'span12 offset2', false)?>

<form method="post" id="clear-cache-form" action="<?php echo $this->url('/dashboard/system/maintenance/cache', 'clear_cache')?>">
    <div class="ccm-pane-body">
        <?php echo $this->controller->token->output('clear_cache')?>
        <p><?php echo t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.')?></p>
    </div>
    <div class="ccm-pane-footer">
        <?
        print $interface->submit(t('Clear Cache'), 'clear-cache-form', 'left','primary');
        ?>
    
    </div>    
</form>
    
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
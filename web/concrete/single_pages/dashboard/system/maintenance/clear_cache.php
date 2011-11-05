<? defined('C5_EXECUTE') or die("Access Denied."); ?>
    
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Clear Cache'), false, 'span12 offset2', false)?>

<form method="post" id="clear-cache-form" action="<?php echo $this->url('/dashboard/system/maintenance/clear_cache', 'do_clear')?>">
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
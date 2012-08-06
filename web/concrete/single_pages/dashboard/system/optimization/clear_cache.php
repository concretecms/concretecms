<? defined('C5_EXECUTE') or die("Access Denied."); ?>
    
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Clear Cache'), false, 'span10 offset1')?>

<form method="post" id="clear-cache-form" action="<?php echo $this->url('/dashboard/system/optimization/clear_cache', 'do_clear')?>">
	<?php echo $this->controller->token->output('clear_cache')?>
    <p><?php echo t('If your site is displaying out-dated information, or behaving unexpectedly, it may help to clear your cache.')?></p>
    <? print $interface->submit(t('Clear Cache'), 'clear-cache-form', 'left', false, array('style' => 'margin-top:5px;')); ?>
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
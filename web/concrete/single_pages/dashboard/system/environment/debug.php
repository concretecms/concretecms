<? defined('C5_EXECUTE') or die("Access Denied.");?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Debug Level'), false, 'span6 offset3', false)?>

<form method="post" id="debug-form" action="<?php echo $this->url('/dashboard/system/environment/debug', 'update_debug')?>">
<div class="ccm-pane-body">
<?php echo $this->controller->token->output('update_debug')?>
	
	
	<div class="control-group">
	
    <div class="controls">    
    <label class="radio">
	<input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_PRODUCTION?>" <?php  if ($debug_level == DEBUG_DISPLAY_PRODUCTION) { ?> checked <?php  } ?> /> <span><?php echo t('Hide errors from site visitors.')?> </span>
    </label>

    <label class="radio">
	<input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_ERRORS?>" <?php  if ($debug_level == DEBUG_DISPLAY_ERRORS) { ?> checked <?php  } ?> /> <span><?php echo t('Show errors in page.')?></span>
    </label>
    
	</div>
	</div>
	
</div>

<div class="ccm-pane-footer">
	<?
	print $interface->submit(t('Save'), 'debug-form', 'right','primary');
	?>

</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?> 
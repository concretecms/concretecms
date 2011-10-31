<? defined('C5_EXECUTE') or die("Access Denied.");?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Debug Level'), false, 'span12 offset2', false)?>

<form method="post" id="debug-form" action="<?php echo $this->url('/dashboard/system/developer/debug', 'update_debug')?>">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('update_debug')?>
	
	
	<div class="block-message alert-message info"><?php echo t('Note: these are global settings. If enabled, PHP errors will be displayed to all visitors of the site.')?></div>
    
	<div class="clearfix">
	
    <div class="input">
    
    <ul class="inputs-list">
    <li>
    <label>
	<input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_PRODUCTION?>" <?php  if ($debug_level == DEBUG_DISPLAY_PRODUCTION) { ?> checked <?php  } ?> /> <?php echo t('Production')?> 
    </label>
    <span class="help-block">
       <?php echo t('PHP errors and database exceptions will be suppressed.')?>
    </span>
    </li>
	<li>
    <label>
	<input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_ERRORS?>" <?php  if ($debug_level == DEBUG_DISPLAY_ERRORS) { ?> checked <?php  } ?> /> <?php echo t('Development')?> 
    </label>
    <span class="help-block">
       <?php echo t('PHP errors and database exceptions will be displayed.')?>
    </span>
    </li>
    </ul>
    
	</div>
	</div>
	
</div>

<div class="ccm-pane-footer">
	<?
	print $interface->submit(t('Set Debug Level'), 'debug-form', 'left','primary');
	?>

</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?> 
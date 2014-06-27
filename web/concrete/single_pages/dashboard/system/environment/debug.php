<? defined('C5_EXECUTE') or die("Access Denied.");?>

<form method="post" id="debug-form" action="<?php echo $view->url('/dashboard/system/environment/debug', 'update_debug')?>">
<?php echo $this->controller->token->output('update_debug')?>
	

    <div class="form-group">
        <div class="radio">
            <label>
                <input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_PRODUCTION?>" <?php  if ($debug_level == DEBUG_DISPLAY_PRODUCTION) { ?> checked <?php  } ?> /> <span><?php echo t('Hide errors from site visitors.')?> </span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="debug_level" value="<?php echo DEBUG_DISPLAY_ERRORS?>" <?php  if ($debug_level == DEBUG_DISPLAY_ERRORS) { ?> checked <?php  } ?> /> <span><?php echo t('Show errors in page.')?></span>
            </label>
        </div>
    </div>
	

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <?
        print $interface->submit(t('Save'), 'debug-form', 'right','btn-primary');
        ?>
    </div>
</div>
</form>
<? defined('C5_EXECUTE') or die("Access Denied.");

// Helpers
$h = Loader::helper('concrete/interface');
$d = Loader::helper('concrete/dashboard');
?>

<?php print $d->getDashboardPaneHeaderWrapper(t('Timezone'), false, 'span8 offset4', false); ?>

<form method="post" id="user-timezone-form" action="<?php echo $this->action('update') ?>">

     <?php echo $this->controller->token->output('update_timezone')?>
     
    <div class="ccm-pane-body">
    
    	<div class="clearfix inputs-list">
            <label>
                <input type="checkbox" name="user_timezones" value="1" <?php if ($user_timezones) { ?> checked <?php } ?> />
                <span><?php echo t('Enable user defined time zones.') ?></span>
            </label>
        </div>
        
    </div>
     
     <div class="ccm-pane-footer">
          <? print $interface->submit(t('Save'), 'user-timezone-form', 'right', 'primary'); ?>
     </div>
     
</form>

<?php print $d->getDashboardPaneFooterWrapper(false); ?>
<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/interface'); ?>
<?php $d = Loader::helper('concrete/dashboard');
print $d->getDashboardPaneHeaderWrapper(t('Timezone'), false, 'span8 offset4', false); ?>
<form method="post" id="user-timezone-form" action="<?php echo $this->action('update') ?>">
     <?php echo $this->controller->token->output('update_timezone')?>
     <div class="ccm-pane-body">
          <div class="ccm-dashboard-checkbox"><input type="checkbox" name="user_timezones" value="1" style="vertical-align: middle" <?php if ($user_timezones) { ?> checked <?php } ?> /> <?php echo t('Enable user defined time zones.') ?></div>
          <div class="ccm-dashboard-description"><?php echo t('Allows site members to display date/time information in their time zone.') ?></div>
     </div>
     <div class="ccm-pane-footer">
          <?
          print $interface->submit(t('Save'), 'user-timezone-form', 'left', 'primary');
          ?>
     </div>
</form>
<?php print $d->getDashboardPaneFooterWrapper(false); ?>
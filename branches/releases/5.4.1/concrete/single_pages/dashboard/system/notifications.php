<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1><span><?php echo t('System Notifications')?></span></h1>
<div class="ccm-dashboard-inner">

<?php  if (count($notifications) == 0) { ?>
	<p><?php echo t('There are no notifications.')?></p>
<?php  } else { ?>

	<?php  Loader::element('dashboard/notification_list', array('notifications' => $notifications)); ?>


<?php  } ?>

</div>
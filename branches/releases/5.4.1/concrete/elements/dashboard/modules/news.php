<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a style="position: absolute; top: 9px; right: 10px; z-index: 10" href="<?php echo View::url('/dashboard/system/notifications')?>"><?php echo t('View all &gt;')?></a>

<?php  
if ($total > 0) { 
	Loader::element('dashboard/notification_list', array('notifications' => $notifications, 'isDashboardModule' => true));
} else { ?>
	<p><?php echo t('There are no notifications.')?></p>
<?php  } ?>

<?php  if ($total > 1) { ?>
	<p><a href="<?php echo View::url('/dashboard/system/notifications')?>"><?php echo t('View all %s notifications &gt;', $total)?></a></p>
<?php  } ?>
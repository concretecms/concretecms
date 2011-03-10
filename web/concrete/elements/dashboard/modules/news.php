<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a style="position: absolute; top: 9px; right: 10px; z-index: 10" href="<?=View::url('/dashboard/system/notifications')?>"><?=t('View all &gt;')?></a>

<? 
if ($total > 0) { 
	Loader::element('dashboard/notification_list', array('notifications' => $notifications, 'isDashboardModule' => true));
} else { ?>
	<p><?=t('There are no notifications.')?></p>
<? } ?>

<? if ($total > 1) { ?>
	<p><a href="<?=View::url('/dashboard/system/notifications')?>"><?=t('View all %s notifications &gt;', $total)?></a></p>
<? } ?>
<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::element('dashboard/notification_list', array('notifications' => $notifications, 'isDashboardModule' => true));
?>

<? if ($total > 1) { ?>
	<p><a href="<?=View::url('/dashboard/system/notifications')?>"><?=t('View all %s notifications &gt;', $total)?></a></p>
<? } ?>
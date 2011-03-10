<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1><span><?=t('System Notifications')?></span></h1>
<div class="ccm-dashboard-inner">

<? if (count($notifications) == 0) { ?>
	<p><?=t('There are no notifications.')?></p>
<? } else { ?>

	<? Loader::element('dashboard/notification_list', array('notifications' => $notifications)); ?>


<? } ?>

</div>
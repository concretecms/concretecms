<?
defined('C5_EXECUTE') or die(_("Access Denied."));
foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php echo $item->getSystemNotificationURL(); ?>"><?php echo $item->getSystemNotificationTitle(); ?></a></h4>
	<h5><?php echo date(t('F jS'), strtotime($item->getSystemNotificationDateTime())); ?></h5>
	<?php echo $item->getSystemNotificationDescription(); ?>
	</div>
<? } ?>

<h2><?=t('Read More')?></h2>

<p><?=t('Read more concrete5 news <a href="%s">at the concrete5 Developer Center</a>', $feed_read_more)?>.</p>
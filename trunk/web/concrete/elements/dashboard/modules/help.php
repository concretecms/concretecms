<?
defined('C5_EXECUTE') or die(_("Access Denied."));
foreach($posts as $item) { ?>
	
	<div class="post">
	<h4><a href="<?php echo $item->getSystemNotificationURL(); ?>"><?php echo $item->getSystemNotificationTitle(); ?></a></h4>
	<h5><?php echo date(t('F jS'), strtotime($item->getSystemNotificationDateTime())); ?></h5>
	<?php echo $item->getSystemNotificationDescription(); ?>
	</div>
<? } ?>

<h2><?=t('Search Documentation')?></h2>
<form method="post" action="http://www.concrete5.org/search/">
<input type="text" name="query" style="width: 130px" />
<input name="search_paths[]" type="hidden" value="/help" />
<input type="hidden" name="do" value="search" />
<input type="submit" value="<?=t('Search')?>" />
</form>
<br/>

<h2><?=t('Full Documentation')?></h2>
<div><?=t('Full documentation is available <a href="%s">at Concrete5.org</a>', 'http://www.concrete5.org/docs/')?>.</div><br/>

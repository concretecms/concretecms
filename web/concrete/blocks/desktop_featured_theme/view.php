<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<h6><?=t('Featured Theme')?></h6>
<?php if (is_object($remoteItem)) {
    ?>
	<div class="clearfix">
	<img src="<?=$remoteItem->getRemoteIconURL()?>" width="50" height="50" class="pull-right" style="margin-left: 10px; margin-bottom: 10px" />
	<h4><?=$remoteItem->getName()?></h4>
	<p><?=$remoteItem->getDescription()?></p>
	</div>
	
	<a href="<?=$remoteItem->getRemoteURL()?>" class="btn btn-default"><?=t('Learn More')?></a>
<?php 
} else {
    ?>
	<p><?=t("Cannot retrieve data from the concrete5 marketplace.")?></p>
<?php 
} ?>
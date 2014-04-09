<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h6><?=t('Featured Theme')?></h6>
<? if (is_object($remoteItem)) { ?>
	<div class="clearfix">
	<img src="<?=$remoteItem->getRemoteIconURL()?>" width="97" height="97" style="float: left; margin-right: 10px; margin-bottom: 10px" />
	<h3><?=$remoteItem->getName()?></h3>
	<p><?=$remoteItem->getDescription()?></p>
	</div>
	
	<a href="javascript:void(0)" onclick="ccm_getMarketplaceItemDetails(<?=$remoteItem->getMarketplaceItemID()?>)" href="<?=$remoteItem->getRemoteURL()?>" class="btn"><?=t('Learn More')?></a>
<? } else { ?>
	<p><?=t("Cannot retrieve data from the concrete5 marketplace.")?></p>
<? } ?>
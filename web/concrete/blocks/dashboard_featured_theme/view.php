<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h2><?=t('Featured Theme')?></h2>

<div class="clearfix">
<img src="<?=$remoteItem->getRemoteIconURL()?>" width="97" height="97" style="float: left; margin-right: 10px; margin-bottom: 10px" />
<h3><?=$remoteItem->getName()?></h3>
<p><?=$remoteItem->getDescription()?></p>
</div>

<a href="javascript:void(0)" onclick="ccm_openThemeLauncher(<?=$remoteItem->getMarketplaceItemID()?>)" href="<?=$remoteItem->getRemoteURL()?>" class="btn"><?=t('Learn More')?></a>
<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1><span><?=t("Connect to the Community")?></span></h1>
<div class="ccm-dashboard-inner">
<? 
	$mi = Marketplace::getInstance();
	if ($mi->isConnected()) { ?>
		
		<? if ($isNew) { ?>
			<?=t('Your site is now connected to the concrete5 community!')?>
		<? } else { ?>
			<?=t('Your site is currently connected to the concrete5 community.')?>
		<? } ?>
		
	<?
	
	} else {
		print $mi->getMarketplaceFrame();
	}
?>

</div>
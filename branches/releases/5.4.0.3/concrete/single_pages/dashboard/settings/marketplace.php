<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><span><?php echo t("Connect to the Community")?></span>
<div class="ccm-dashboard-inner">
<?php  
	$mi = Marketplace::getInstance();
	if ($mi->isConnected()) { ?>
		
		<?php  if ($isNew) { ?>
			<?php echo t('Your site is now connected to the concrete5 community!')?>
		<?php  } else { ?>
			<?php echo t('Your site is currently connected to the concrete5 community.')?>
		<?php  } ?>
		
	<?php 
	
	} else {
		$mi->outputMarketplaceFrame();
	}
?>

</div>
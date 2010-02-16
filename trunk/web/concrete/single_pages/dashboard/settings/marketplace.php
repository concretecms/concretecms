<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><span><?=t("Connect to the Community")?></span>
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
	
	} else { ?>
	
			<iframe width="100%" height="530px" style="border: 0px" src="<?=$url?>"></iframe>
		
		<?			
	}
?>

</div>
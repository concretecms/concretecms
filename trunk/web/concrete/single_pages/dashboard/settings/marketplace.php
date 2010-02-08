<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><span><?=t("Connect to the Community")?></span>
<div class="ccm-dashboard-inner">
<? 
	if (Marketplace::isConnected()) { ?>
		
		<?=t('Your site is currently connect to the concrete5 community.')?>
	
	<?
	
	} else { ?>
	
			<iframe width="100%" height="530px" style="border: 0px" src="<?=$url?>"></iframe>
		
		<?			
	}
?>

</div>
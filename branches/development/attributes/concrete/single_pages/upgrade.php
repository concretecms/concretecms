<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><?=t('Upgrade Concrete5')?></h1>

<?=$message?>


<? if ($do_upgrade) { ?>

	<?=t('To proceed with the upgrade, click below.')?>

	<div class="ccm-form">
	<div class="ccm-button">
	<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade.php">
	<input type="submit" name="do_upgrade" class="ccm-input-submit" value="<?=t('Upgrade')?> &gt;" />
	</form>
	</div>
	</div>
	
<? } else { ?>
<br/><br/>
<a href="<?=DIR_REL?>/"><?=t('Back to Home')?></a>.
<? } ?>
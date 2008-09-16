<h1>Upgrade Concrete5</h1>

<?=$message?>


<? if ($do_upgrade) { ?>

	To proceed with the upgrade, click below.
	<div class="ccm-form">
	<div class="ccm-button">
	<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade.php">
	<input type="submit" name="do_upgrade" class="ccm-input-submit" value="Upgrade &gt;" />
	</form>
	</div>
	</div>
	
<? } else { ?>
<br/><br/>
	<a href="<?=DIR_REL?>/">Back to your site &gt;</a>
<? } ?>
<h1>Upgrade Concrete5</h1>

<?php echo $message?>


<?php  if ($do_upgrade) { ?>

	To proceed with the upgrade, click below.
	<div class="ccm-form">
	<div class="ccm-button">
	<form method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade.php">
	<input type="submit" name="do_upgrade" class="ccm-input-submit" value="Upgrade &gt;" />
	</form>
	</div>
	</div>
	
<?php  } else { ?>
<br/><br/>
	<a href="<?php echo DIR_REL?>/">Back to your site &gt;</a>
<?php  } ?>
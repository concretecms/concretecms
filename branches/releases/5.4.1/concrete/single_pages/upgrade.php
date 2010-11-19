<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1><?php echo t('Upgrade Concrete5')?></h1>
<p>
<?php echo $message?>
</p>

<?php  if($had_failures) { ?>
<div class="ccm-error" style="padding:12px 0 12px 0">
	<?php echo t('These errors are most likely related to incompatible add-ons, please upgrade any add-ons and re-run to this script to complete the conversion of your data.')?>
</div>
<?php  } ?>

<?php echo $completeMessage?>

<?php  if ($do_upgrade) { ?>
	<?php echo t('To proceed with the upgrade, click below.')?>

	<div class="ccm-form">
	<div class="ccm-button">
	<form method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade.php">
	<input type="submit" name="do_upgrade" class="ccm-input-submit" value="<?php echo t('Upgrade')?> &gt;" />
	</form>
	</div>
	</div>
<?php  } else { ?>
    <br/>
    <a href="<?php echo DIR_REL?>/"><?php echo t('Back to Home')?></a>.
    <?php  if(!isset($hide_force) || !$hide_force) { ?>
        <p>
        <?php echo t('<a href="%s">Click here</a> if you would like to re-run this script.', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools/required/upgrade?force=1')?>
        </p>
    <?php  } ?>
<?php  } ?>
<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><?=t('Upgrade Concrete5')?></h1>
<p>
<?=$message?>
</p>

<? if($had_failures) { ?>
<div class="ccm-error" style="padding:12px 0 12px 0">
	<?=t('These errors are most likely related to incompatible add-ons, please upgrade any add-ons and re-run to this script to complete the conversion of your data.')?>
</div>
<? } ?>

<?=$completeMessage?>

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
    <br/>
    <a href="<?=DIR_REL?>/"><?=t('Back to Home')?></a>.
    <?php if(!isset($hide_force) || !$hide_force) { ?>
        <p>
        <a href="<?=DIR_REL?>/index.php/tools/required/upgrade?force=1"><?=t('click here')?></a>
        <?=t('If you would like to re-run this script.')?>
        </p>
    <? } ?>
<? } ?>
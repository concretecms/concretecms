<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="row">
<div class="span10 offset1">

<div class="page-header">
	<h1><?=t('Upgrade concrete5')?></h1>
</div>
<p>
<?=$message?>
</p>

<? if($had_failures) { ?>
<div class="alert-message block-message error">
	<?=t('These errors are most likely related to incompatible add-ons, please upgrade any add-ons and re-run to this script to complete the conversion of your data.')?>
</div>
<? } ?>

<? if ($completeMessage) { ?>
	<?=$completeMessage?>
<? } ?>

<? if ($do_upgrade) { ?>
<p>	<?=t('To proceed with the upgrade, click below.')?></p>


	<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade.php">
	<div class="well" style="text-align: right">
	<input type="submit" name="do_upgrade" class="ccm-input-submit btn primary" value="<?=t('Upgrade')?> &gt;"  />
	</div>
	</form>



<? } else { ?>

	<div class="well" style="text-align: left">
	    <a href="<?=DIR_REL?>/" class="btn"><?=t('Back to Home')?></a>
  	</div>
	
	<?php if(!isset($hide_force) || !$hide_force) { ?>
        <p>
        <?=t('<a href="%s">Click here</a> if you would like to re-run this script.', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools/required/upgrade?force=1')?>
        </p>
    <? } ?>
<? } ?>

</div>
</div>
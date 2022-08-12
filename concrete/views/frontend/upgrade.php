<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<div class="page-header">
	<h1><?=t('Upgrade Concrete')?></h1>
</div>
<?php if (!empty($status)) { ?>
	<p><?=$status?></p>
<?php } ?>

<?php if (!empty($had_failures)) { ?>
	<div class="alert-message block-message error">
	<?=t('These errors are most likely related to incompatible add-ons, please upgrade any add-ons and re-run to this script to complete the conversion of your data.')?>
	</div>
<?php } ?>

<?php if (!empty($completeMessage)) { ?>
	<?=$completeMessage?>
<?php } ?>

<?php if (!empty($do_upgrade)) { ?>
	<p><?=t('To proceed with the upgrade, click below.')?></p>

	<form method="post" action="<?=$controller->action('submit')?>">
	<div class="card card-body bg-light" style="text-align: left">
	<input type="submit" name="do_upgrade" class="btn btn-primary" value="<?=t('Upgrade')?>"  />
	</div>
	</form>

<?php } else { ?>

	<div class="card card-body bg-light" style="text-align: left">
	<a href="<?=DIR_REL?>/" class="btn btn-primary"><?=t('Back to Home')?></a>
	<?php if (!isset($hide_force) || !$hide_force) { ?>
        <a href="<?=DIR_REL . '/' . DISPATCHER_FILENAME . '/ccm/system/upgrade?force=1'?>" class="btn btn-secondary"><?=t('Re-Run Upgrade Script')?></a>
        <?php } ?>
  	</div>
	
<?php } ?>

</div>
</div>

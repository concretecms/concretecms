<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="forgotPassword">
	<h4><?= t('Email Validated') ?></h4>
	<div class="help-block">
		<?= t('This email address has been validated! You may now access the features of this site.') ?>
	</div>
	<a href="<?= URL::to('/') ?>" class="btn btn-block btn-primary">
		<?= t('Continue') ?>
	</a>
</div>

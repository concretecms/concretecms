<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (!empty($supportsLegacy)): ?>
    <a href="<?=URL::to('/dashboard/reports/forms/legacy')?>" class="btn btn-secondary"><?=t('Legacy Forms')?></a>
<?php endif ?>

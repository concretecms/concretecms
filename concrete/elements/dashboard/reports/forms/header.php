<?php
defined('C5_EXECUTE') or die("Access Denied.");


if ($supportsLegacy) { ?>

    <div class="ccm-dashboard-header-buttons">

    <a href="<?=URL::to('/dashboard/reports/forms/legacy')?>" class="btn btn-default"><?=t('Legacy Forms')?></a>

    </div>

<?php } ?>
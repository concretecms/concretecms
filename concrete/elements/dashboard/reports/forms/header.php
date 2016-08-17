<?php
defined('C5_EXECUTE') or die("Access Denied.");


if ($supportsLegacy) { ?>

    <a href="<?=URL::to('/dashboard/reports/forms/legacy')?>" class="btn btn-default"><?=t('Legacy Forms')?></a>

<?php } ?>
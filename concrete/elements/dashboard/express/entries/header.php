<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a class="btn btn-default" href="<?=$exportURL?>"><i class="fa fa-download"></i> <?=t('Export to CSV') ?></a>
<a class="btn btn-primary" href="<?=$createURL?>">
    <i class="fa fa-plus"></i> <?=t('New %s', $entity->getName())?>
</a>

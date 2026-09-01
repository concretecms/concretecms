<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div>
    <a href="<?=$link ?? '#'?>"><?=h($title ?? t('No Title'))?></a>
    <?php if (isset($description) && $description) { ?>
        <div><?=h($description)?></div>
    <?php } ?>
</div>

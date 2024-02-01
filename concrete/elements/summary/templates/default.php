<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div>
    <a href="<?=$link?>"><?=$title?></a>
    <?php if (isset($description) && $description) { ?>
        <div><?=$description?></div>
    <?php } ?>
</div>

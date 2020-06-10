<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
    <div class="col-4">
        <img class="img-fluid" src="<?=$thumbnail?>">
    </div>
    <div class="col-8">
        <a href="<?=$link?>"><?=$title?></a>
        <?php if ($date) { ?>
            <div><small class="text-muted"><?=date('F d, Y', (string) $date)?></small></div>
        <?php } ?>
        <?php if ($description) { ?>
            <div><?=$description?></div>
        <?php } ?>
    </div>
</div>

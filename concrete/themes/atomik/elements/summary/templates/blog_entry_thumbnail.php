<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-summary-template-blog-entry-thumbnail">
    <a href="<?=$link?>"><img class="img-fluid" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>"></a>
    <div class="mb-4">
        <h5><a href="<?=$link?>"><?=$title?></a></h5>
        <?php if ($date) { ?>
            <div class="text-center"><small class="text-muted"><?=date('F d, Y', (string) $date)?></small></div>
        <?php } ?>
    </div>
</div>

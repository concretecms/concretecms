<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-summary-template-blog-entry-thumbnail">
    <a href="<?=$link?>" class="card">
        <div class="position-relative">
            <div class="card-img-top ccm-summary-template-blog-entry-thumbnail-image-overlay"></div>
            <img class="card-img-top" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>">
        </div>
        <div class="card-body">
            <h5 class="card-title"><?=$title?></h5>
            <?php if ($date) { ?>
                <p class="card-text text-center"><small class="text-muted"><?=date('F d, Y', (string) $date)?></small></p>
            <?php } ?>
        </div>
    </a>
</div>

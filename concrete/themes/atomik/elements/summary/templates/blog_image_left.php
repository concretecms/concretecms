<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$page = Page::getCurrentPage();
?>
<div class="ccm-summary-template-blog-image-left">
    <div class="row">
        <div class="col-md-6">
            <img class="img-fluid" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>">
        </div>
        <div class="col-md-6">
            <h5 class=""><a href="<?=$link?>"><?=$title?></a></h5>
            <?php
            $element = Element::get('byline', $page, ['author' => $author, 'date' => $date]);
            $element->render();
            ?>
            <?php if ($description) { ?>
                <p><?=$description?></p>
            <?php } ?>
        </div>
    </div>
</div>

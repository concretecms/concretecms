<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$view = new \Concrete\Core\View\View();
$view->setViewTheme('atomik');
?>
<div class="ccm-summary-template-blog-image-right mb-3 mb-md-0">
    <div class="row">
        <div class="col-md-6 order-2 order-md-1">
            <h5 class=""><a href="<?=$link?>"><?=$title?></a></h5>
            <?php
            $view->inc('elements/byline.php', ['author' => $author, 'date' => $date]);
            ?>
            <?php if ($description) { ?>
                <p><?=$description?></p>
            <?php } ?>
        </div>
        <div class="col-md-6 order-1 order-md-2">
            <img class="img-fluid mb-md-0 mb-3" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>">
        </div>
    </div>
</div>

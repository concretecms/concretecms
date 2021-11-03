<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$view = new \Concrete\Core\View\View();
$view->setViewTheme('atomik');
?>
<div class="ccm-summary-template-blog-image-left mb-3 mb-md-0">
    <div class="row">
        <div class="col-md-6">
            <img class="img-fluid mb-md-0 mb-3" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>">
        </div>
        <div class="col-md-6">
            <h5 class=""><a href="<?=$link?>"><?=$title?></a></h5>
            <?php
            $view->inc('elements/byline.php', ['author' => $author, 'date' => $date]);
            ?>
            <?php if ($description) { ?>
                <p><?=$description?></p>
            <?php } ?>
        </div>
    </div>
</div>

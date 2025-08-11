<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$view = new \Concrete\Core\View\View();
$view->setViewTheme('atomik');
?>
<div class="ccm-summary-template-blog-image-left mb-3 mb-md-0">
    <div class="row">
        <div class="col-md-6">
            <a href="<?=$link?>"><img class="img-fluid mb-md-0 mb-3" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>"></a>
        </div>
        <div class="col-md-6">
            <h5 class=""><a href="<?=$link?>"><?=h($title)?></a></h5>
            <?php
            if (isset($author) || isset($date)) {
                $view->inc('elements/byline.php', ['author' => $author ?? '', 'date' => $date ?? '']);
            }
            ?>
            <?php if (isset($description)) { ?>
                <p><?=$description?></p>
            <?php } ?>
        </div>
    </div>
</div>

<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$page = Page::getCurrentPage();
?>

<div class="ccm-summary-template-blog-image-top">
    <img class="img-fluid mb-3" src="<?=$thumbnail->getThumbnailURL('blog_entry_thumbnail')?>">
    <h5 class=""><a href="<?=$link?>"><?=$title?></a></h5>
    <?php
    $element = Element::get('byline', $page, ['author' => $author, 'date' => $date]);
    $element->render();
    ?>
    <?php if ($description) { ?>
        <p><?=$description?></p>
    <?php } ?>
</div>

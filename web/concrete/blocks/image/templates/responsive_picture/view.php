<?php defined('C5_EXECUTE') or die("Access Denied.");

$view->requireAsset('javascript', 'picturefill');

$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();

?>

<? if (strlen($linkURL)) { ?>
    <a href="<?=$linkURL?>">
<? } ?>

<picture>
    <? foreach($pt->getThemeResponsiveImageMap() as $thumbnail => $width) {
        $type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle($thumbnail);
        $src = $f->getThumbnailURL($type->getBaseVersion());
        ?>
        <source class="ccm-image-block primary img-responsive"  <? if ($width != '0') { ?>media="(min-width: <?=$width?>)" <? } ?> srcset="<?=$src?>">
    <? } ?>
    <p><?=$altText?></p>
    <img class="ccm-image-block img-responsive" src="<?=$relPath?>" alt="<?=$altText?>">
</picture>

<?if (strlen($linkURL)) { ?>
    </a>
<?}?>

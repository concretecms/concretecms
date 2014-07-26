<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<!--
<?
if (strlen($relPathHover)) {
	$mouseOvers = ' onmouseover="this.src=\''.$relPathHover.'\'" onmouseout="this.src=\''.$relPath.'\'"';
}
if (strlen($linkURL)) { ?>
	<a href="<?=$linkURL?>"<?=strlen($hoverImage)?' class="ccm-image-hover"':''?>>
<?}?>
<img class="ccm-image-block primary img-responsive" alt="<?=$altText?>" src="<?=$relPath?>" <?=$sizeStr?>/>
<? if($relPathHover) { ?>
	<img class="ccm-image-block alternate img-responsive" alt="<?=$altText?>" src="<?=$relPathHover?>" <?=$sizeStr?>/>
<? }?>
<?if (strlen($linkURL)) { ?>
	</a>
<?}?>
//-->

<?
$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();
?>

<picture>
    <? foreach($pt->getThemeResponsiveImageMap() as $thumbnail => $width) {
        $type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle($thumbnail);
        $src = $f->getThumbnailURL($type->getBaseVersion());
    ?>
        <source class="img-responsive"  <? if ($width != '0') { ?>media="(min-width: <?=$width?>)" <? } ?> srcset="<?=$src?>">
    <? } ?>
    <p><?=$altText?></p>
    <img class="img-responsive" src="<?=$relPath?>" alt="<?=$altText?>">
</picture>

<?
if ($fOnstateID) {
    $view->inc('js/hover.php');
}
?>
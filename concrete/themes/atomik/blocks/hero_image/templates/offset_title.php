<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
$heightRatio = '1.0';
if (isset($height)) {
    $heightRatio = $height / 100;
}
?>
<?php if ($image) { ?>

<div class="ccm-block-hero-image-offset-title" data-block-id="<?=$bID?>">

    <div class="ccm-block-hero-image-offset-image-container">
        <img src="<?=$image->getURL()?>" data-height-ratio="<?=$heightRatio?>">
    </div>

    <div class="ccm-block-hero-image-text">

        <?php if ($title) { ?>
            <h1><?=$title?></h1>
        <?php } ?>

        <?php if ($body) { ?>
            <?=$body?>
        <?php } ?>

        <?php if (isset($button)) { ?>

            <div class="mt-4"><?=$button?></div>

        <?php } ?>

    </div>

</div>

<?php } ?>

<script type="text/javascript">
    $(function() {
        $(window).trigger('offsetTitleBlockLoaded')
    })
</script>
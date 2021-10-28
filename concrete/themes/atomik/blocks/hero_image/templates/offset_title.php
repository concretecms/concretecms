<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if ($image) { ?>

<div class="ccm-block-hero-image-offset-title">

    <img src="<?=$image->getURL()?>" class="img-fluid">

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
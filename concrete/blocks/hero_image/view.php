<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if ($image) { ?>

<div data-transparency="element" class="ccm-block-hero-image" <?php if ($height) { ?>style="min-height: <?=$height?>vh"<?php } ?>>
    <div class="ccm-block-hero-image-cover" <?php if ($height) { ?>style="min-height: <?=$height?>vh"<?php } ?>></div>

    <div style="background-image: url('<?=$image->getURL()?>'); <?php if ($height) { ?>min-height: <?=$height?>vh<?php } ?>" class="ccm-block-hero-image-image"></div>

    <div class="ccm-block-hero-image-text" <?php if ($height) { ?>style="min-height: <?=$height?>vh"<?php } ?>>
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
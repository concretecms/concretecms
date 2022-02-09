<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>


<div class="ccm-block-feature-link">

    <div class="ccm-block-feature-link-text">

        <?php if ($title) { ?>
            <<?=$titleFormat?>><?=$title?></<?=$titleFormat?>>
        <?php } ?>

        <?php if ($body) { ?>
            <?=$body?>
        <?php } ?>

        <?php if (isset($button)) { ?>

            <div class="mt-4"><?=$button?></div>

        <?php } ?>

    </div>

</div>
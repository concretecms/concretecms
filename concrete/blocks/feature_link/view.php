<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var string|null $title */
/** @var string $titleFormat */
/** @var string|null $body */
/** @var HtmlObject\Link|null $button */
?>


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
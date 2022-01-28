<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var string|null $image */
/** @var string|null $name */
/** @var string|null $position */
/** @var string|null $company */
/** @var string|null $companyURL */
/** @var string|null $paragraph */
/** @var string|null $awardImage */
?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial">
        <?php if ($image) { ?>
            <div class="ccm-block-testimonial-image"><?=$image?></div>
        <?php } ?>

        <div class="ccm-block-testimonial-quote">
            <div class="ccm-block-testimonial-text">

                <div class="ccm-block-testimonial-name">
                    <?=h($name)?>
                </div>

            <?php if ($position && $company && $companyURL) { ?>
                <div class="ccm-block-testimonial-position">
                    <?=sprintf('%s, <a href="%s">%s</a>', h($position), $companyURL, h($company))?>
                </div>
            <?php } ?>

            <?php if ($position && !$company && $companyURL) { ?>
                <div class="ccm-block-testimonial-position">
                    <?=sprintf('<a href="%s">%s</a>', $companyURL, h($position))?>
                </div>
            <?php } ?>

            <?php if ($position && $company && !$companyURL) { ?>
                <div class="ccm-block-testimonial-position">
                    <?=sprintf('%s, %s', h($position), h($company))?>
                </div>
            <?php } ?>

            <?php if ($position && !$company && !$companyURL) { ?>
                <div class="ccm-block-testimonial-position">
                    <?=h($position)?>
                </div>
            <?php } ?>

            <?php if ($paragraph) { ?>
                <div class="ccm-block-testimonial-paragraph"><?=h($paragraph)?></div>
            <?php } ?>

            </div>
        </div>

        <?php if ($awardImage) { ?>
            <div class="ccm-block-testimonial-award-image"><?php echo $awardImage ?></div>
        <?php } ?>

    </div>
</div>
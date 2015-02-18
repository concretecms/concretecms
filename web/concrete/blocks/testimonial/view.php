<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial">
        <?php if ($image): ?>
            <div class="ccm-block-testimonial-image"><?=$image?></div>
        <?php endif; ?>

        <div class="ccm-block-testimonial-text">

            <div class="ccm-block-testimonial-name">
                <?=$name?>
            </div>

        <?php if ($position && $company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, <a href="%s">%s</a>', $position, $companyURL, $company)?>
            </div>
        <?php endif; ?>

        <?php if ($position && !$company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('<a href="%s">%s</a>', $companyURL, $position)?>
            </div>
        <?php endif; ?>

        <?php if ($position && $company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, %s', $position, $company)?>
            </div>
        <?php endif; ?>

        <?php if ($position && !$company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=$position?>
            </div>
        <?php endif; ?>


        <?php if ($paragraph): ?>
            <div class="ccm-block-testimonial-paragraph"><?=$paragraph?></div>
        <?php endif; ?>

        </div>

    </div>

</div>
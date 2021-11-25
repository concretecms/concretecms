<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial-circle">

        <?php if ($image) {
            $imageFile = \Concrete\Core\File\File::getByID($fID);
            if ($imageFile) { ?>
            <div class="ccm-block-testimonial-circle-avatar">
                <img src="<?=$imageFile->getThumbnailURL('testimonial_circle')?>" />
            </div>
            <?php } ?>
        <?php } ?>

        <div class="ccm-block-testimonial-circle-text">

            <h5><?=h($name)?></h5>

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
</div>
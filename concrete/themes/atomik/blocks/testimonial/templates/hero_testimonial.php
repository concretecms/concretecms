<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-testimonial-hero">

    <div class="ccm-block-testimonial-hero-cover"></div>

    <?php
    if ($awardImage) {
        $awardImageFile = \Concrete\Core\File\File::getByID($awardImageID);
        ?>
        <div style="background-image: url('<?=$awardImageFile->getURL()?>')" class="ccm-block-testimonial-hero-image"></div>
    <?php
    } ?>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="ccm-block-testimonial-hero-text">

                    <div class="ccm-block-testimonial-hero-quote">
                        <div class="quote-start"></div>
                            <h4><?=h($paragraph)?></h4>
                        <div class="quote-end"></div>
                    </div>

                    <?php if ($image) {
                        $imageFile = \Concrete\Core\File\File::getByID($fID);
                        ?>
                        <div class="ccm-block-testimonial-hero-avatar"><img src="<?=$imageFile->getURL()?>" /></div>
                    <?php } ?>

                    <div class="ccm-block-testimonial-hero-name">
                        <?=h($name)?>
                    </div>

                    <?php if ($position && $company && $companyURL): ?>
                        <div class="ccm-block-testimonial-hero-position">
                            <?=sprintf('%s - <a href="%s">%s</a>', h($position), $companyURL, h($company))?>
                        </div>
                    <?php endif; ?>

                    <?php if ($position && !$company && $companyURL): ?>
                        <div class="ccm-block-testimonial-hero-position">
                            <?=sprintf('<a href="%s">%s</a>', $companyURL, h($position))?>
                        </div>
                    <?php endif; ?>

                    <?php if ($position && $company && !$companyURL): ?>
                        <div class="ccm-block-testimonial-hero-position">
                            <?=sprintf('%s - %s', h($position), h($company))?>
                        </div>
                    <?php endif; ?>

                    <?php if ($position && !$company && !$companyURL): ?>
                        <div class="ccm-block-testimonial-hero-position">
                            <?=h($position)?>
                        </div>
                    <?php endif; ?>



                </div>


            </div>

        </div>
    </div>


</div>

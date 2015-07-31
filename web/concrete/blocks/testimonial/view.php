<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial">
        <? if ($image): ?>
            <div class="ccm-block-testimonial-image"><?=$image?></div>
        <? endif; ?>

        <div class="ccm-block-testimonial-text">

            <div class="ccm-block-testimonial-name">
                <?=h($name)?>
            </div>

        <? if ($position && $company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, <a href="%s">%s</a>', h($position), $companyURL, h($company))?>
            </div>
        <? endif; ?>

        <? if ($position && !$company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('<a href="%s">%s</a>', $companyURL, h($position))?>
            </div>
        <? endif; ?>

        <? if ($position && $company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, %s', h($position), h($company))?>
            </div>
        <? endif; ?>

        <? if ($position && !$company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=h($position)?>
            </div>
        <? endif; ?>


        <? if ($paragraph): ?>
            <div class="ccm-block-testimonial-paragraph"><?=h($paragraph)?></div>
        <? endif; ?>

        </div>

    </div>

</div>
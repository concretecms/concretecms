<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-testimonial-wrapper">
    <div class="ccm-block-testimonial">
        <? if ($image): ?>
            <div class="ccm-block-testimonial-image"><?=$image?></div>
        <? endif; ?>

        <div class="ccm-block-testimonial-text">

            <div class="ccm-block-testimonial-name">
                <?=$name?>
            </div>

        <? if ($position && $company && $companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, <a href="%s">%s</a>', $position, $companyURL, $company)?>
            </div>
        <? endif; ?>

        <? if ($position && $company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=t('%s, %s', $position, $company)?>
            </div>
        <? endif; ?>

        <? if ($position && !$company && !$companyURL): ?>
            <div class="ccm-block-testimonial-position">
                <?=$position?>
            </div>
        <? endif; ?>

        <? if ($paragraph): ?>
            <div class="ccm-block-testimonial-paragraph"><?=$paragraph?></div>
        <? endif; ?>

        </div>

    </div>

</div>
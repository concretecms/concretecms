<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Building the button
 */

if (isset($button)) {

    if ($buttonStyle == 'outline') {
        $button->addClass('btn btn-outline-' . $buttonColor) ;
    } elseif ($buttonStyle == 'link') {
        $button->addClass('btn btn-link');
    } else {
        $button->addClass('btn btn-' . $buttonColor) ;
    }
    if ($buttonSize) {
        $button->addClass('btn-' . $buttonSize);
    }
    if ($buttonIcon && $button->getValue()) {
        $iconTag = '<span class="ms-3">' . $iconTag . '</span>';
    }

    //Optionally use the setAttribute() function to add additional attributes
    //$button->setAttribute('target', '_blank');

    //Change the button value using the setValue() function
    $button->setValue($button->getValue() . $iconTag);

}
?>

<div class="ccm-block-feature-link top-link">

    <div class="ccm-block-feature-link-text d-flex flex-column flex-xl-row justify-content-between <?php if (isset($buttonColor)) { ?>border-bottom pb-5 border-1 <?php } ?>">
        <div class="col-12 col-xl-9">
        <?php if ($title) { ?>
        <<?=$titleFormat?>><?=$title?></<?=$titleFormat?>>
    <?php } ?>

    <?php if ($body) { ?>
        <?=$body?>
    <?php } ?>
    </div>
    <div class="col-12 col-xl-3 text-xl-end">
    <?php if (isset($button)) { ?>

        <div class=""><?=$button?></div>

    <?php } ?>
    </div>
</div>

</div>
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
        $iconTag = '<span class="me-3">' . $iconTag . '</span>';
    }

    //Optionally use the setAttribute() function to add additional attributes
    //$button->setAttribute('target', '_blank');

    //Change the button value using the setValue() function
    $button->setValue($iconTag . $button->getValue());

}
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

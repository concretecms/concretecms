<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Building the button
 */

$buttonColor = $buttonColor ?? null;

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

/**
 * Bringing in the Image
 */

use Concrete\Core\Html\Image;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Image as HtmlImage;

$app = Application::getFacadeApplication();
/** 
 * @var string $altText
*/

 if (is_object($f) && $f->getFileID()) {
    $imageTag = new HtmlImage();

    if ($f->getTypeObject()->isSVG()) {
        $imageTag->setAttribute("src", $f->getRelativePath());

        $imageTag->addClass('ccm-svg');
    } else {
        /** @var Image $image */
        $image = $app->make('html/image', ['f' => $f]);
        $imageTag = $image->getTag();

            
    }

    $imageTag->addClass('img-fluid bID-' . $bID);

    $altText = $f->getTitle();

    if ($altText) {
        $imageTag->alt(h($altText));
    } else {
        $imageTag->alt('');
    }

    if ($title) {
        $imageTag->title(h($title));
    }

}

?>

<div class="ccm-block-feature-link">
    <?php if (isset($imageTag)) { ?>
        <div class="ccm-block-feature-link-inner d-flex">
            <div class="ccm-block-feature-link-image">
                <?=$imageTag?>
            </div>
            <div class="ccm-block-feature-link-text ms-3">

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
        
    <?php } else { ?>
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
    <?php } ?>

</div>

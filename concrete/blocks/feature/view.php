<?php  defined('C5_EXECUTE') or die("Access Denied."); 

use Concrete\Core\Html\Image;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Image as HtmlImage;

$app = Application::getFacadeApplication();
/** 
 * @var string $altText
*/


?>
<?php
$title = h($title);
if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}
$iconTag = $iconTag ?? '';

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
<div class="ccm-block-feature-item">
<?php if (isset($imageTag)) { ?>
    <div class="ccm-block-feature-item-inner d-flex">
        <div class="ccm-block-feature-image">
            <?=$imageTag?>
        </div>
        <div class="ccm-block-feature-text">
        <?php if ($title) { ?>
            <<?php echo $titleFormat; ?>><?=$title?></<?php echo $titleFormat; ?>>
        <?php } ?>
        <?php if ($paragraph) {
            echo $paragraph;
        } ?>
        </div>
    </div>
        <?php } else { ?>
        <?php if ($title) { ?>
            <<?php echo $titleFormat; ?>><?=$iconTag?> <?=$title?></<?php echo $titleFormat; ?>>
        <?php } ?>
        <?php if ($paragraph) {
            echo $paragraph;
        } ?>
        <?php } ?>
</div>
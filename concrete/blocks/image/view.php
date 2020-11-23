<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Image;

$app = Application::getFacadeApplication();

/** @var int $thumbnailTypeId */
/** @var int $maxWidth */
/** @var int $maxHeight */
/** @var int $cropImage */
/** @var string $altText */
/** @var string $linkURL */
/** @var bool $openLinkInNewWindow */
/** @var Type $thumbnailTypeService */
/** @var Version $foS */
/** @var Version $f */
/** @var array $imgPaths */

$thumbnailTypeService = $app->make(Type::class);
$thumbnailType = $thumbnailTypeService::getByID((int)$thumbnailTypeId);

if (is_object($f) && $f->getFileID()) {
    if ($f->getTypeObject()->isSVG()) {
        $tag = new Image();

        $tag->setAttribute("src", $f->getRelativePath());

        if ($maxWidth > 0) {
            $tag->setAttribute("width", $maxWidth);
        }

        if ($maxHeight > 0) {
            $tag->setAttribute("height", $maxHeight);
        }

        $tag->addClass('ccm-svg');

    } else if ($thumbnailType instanceof \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type) {
        $tag = new Image();
        $tag->setAttribute("src", $f->getThumbnailURL($thumbnailType->getBaseVersion()));
    } elseif ($maxWidth > 0 || $maxHeight > 0) {
        /** @var BasicThumbnailer $im */
        $im = $app->make(BasicThumbnailer::class);
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);
        $tag = new Image();
        $tag->setAttribute("src", $thumb->src);
        $tag->setAttribute("width", $thumb->width);
        $tag->setAttribute("height", $thumb->height);
    } else {
        $image = $app->make('html/image', [$f]);
        $tag = $image->getTag();
    }

    $tag->addClass('ccm-image-block img-fluid bID-' . $bID);

    if ($altText) {
        $tag->setAttribute("alt", h($altText));
    } else {
        $tag->setAttribute('alt', '');
    }

    if ($title) {
        $tag->setAttribute("title", h($title));
    }

    if ($linkURL) {
        echo '<a href="' . $linkURL . '" ' . ($openLinkInNewWindow ? 'target="_blank" rel="noopener noreferrer"' : '') . '>';
    }

    // add data attributes for hover effect
    if (is_object($foS) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
        $tag->addClass('ccm-image-block-hover');
        $tag->setAttribute('data-default-src', $imgPaths['default']);
        $tag->setAttribute('data-hover-src', $imgPaths['hover']);
    }

    echo $tag;

    if ($linkURL) {
        echo '</a>';
    }
} elseif ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item">
        <?php echo t('Empty Image Block.'); ?>
    </div>
<?php } ?>

<?php if (isset($foS) && $foS) { ?>
    <script type="text/javascript">
        var images = document.getElementsByClassName('ccm-image-block-hover');

        for (var i = 0; i < images.length; i++) {
            var image = images[i],
                hoverSrc = image.getAttribute('data-hover-src'),
                defaultSrc = image.getAttribute('data-default-src');
            image.onmouseover = function () {
                image.setAttribute('src', hoverSrc);
            };
            image.onmouseout = function () {
                image.setAttribute('src', defaultSrc);
            };
        }
    </script>
<?php } ?>
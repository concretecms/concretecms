<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Html\Image;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Element;
use HtmlObject\Image as HtmlImage;

$app = Application::getFacadeApplication();

/**
 * @var Concrete\Core\Block\View\BlockView $this
 * @var Concrete\Core\Block\View\BlockView $view
 * @var Concrete\Core\Area\SubArea $a
 * @var Concrete\Core\Entity\Block\BlockType\BlockType $bt
 * @var Concrete\Core\Block\Block $b
 * @var Concrete\Block\Image\Controller $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var int $bID
 *
 * @var bool $cropImage
 * @var int $maxWidth
 * @var int $maxHeight
 * @var string $sizingOption
 * @var Concrete\Core\Entity\File\File|null $f
 * @var Concrete\Core\Entity\File\File|null $foS
 * @var array $imgPaths May be empty, or may contain two strings (with keys 'default' and 'hover')
 * @var string $altText
 * @var string|null $title
 * @var int|null $lazyLoad
 * @var string $linkURL
 * @var bool $openLinkInNewWindow
 * @var array $selectedThumbnailTypes Array keys are the breakpoint handles, array values are the breakpoint IDs
 * @var array $themeResponsiveImageMap Array keys are the responsive breakpoint names, array values are the widths.
 * @var Concrete\Core\Page\Page $c
 */

if (is_object($f) && $f->getFileID()) {
    $imageTag = new HtmlImage();

    $fallbackSrc = $f->getRelativePath();

    if ($f->getTypeObject()->isSVG()) {
        $imageTag->setAttribute("src", $f->getRelativePath());
        if ($maxWidth > 0) {
            $imageTag->setAttribute("width", $maxWidth);
        }
        if ($maxHeight > 0) {
            $imageTag->setAttribute("height", $maxHeight);
        }
        $imageTag->addClass('ccm-svg');
    } else {
        switch ($sizingOption) {
            case "thumbnails_default":
                /** @var Image $image */
                $image = $app->make('html/image', ['f' => $f]);
                $imageTag = $image->getTag();
                $imageTag->setAttribute("width", $f->getAttribute('width'));
                $imageTag->setAttribute("height", $f->getAttribute('height'));
                break;

            case "thumbnails_configurable":
                $sources = [];

                $width = 0;

                if (!$fallbackSrc) {
                    $fallbackSrc = $f->getURL();
                }

                foreach ($selectedThumbnailTypes as $breakpointHandle => $ftTypeID) {

                    foreach ($themeResponsiveImageMap as $themeBreakpointHandle => $themeWidth) {

                        if ($breakpointHandle == $themeBreakpointHandle) {
                            $width = $themeWidth;
                            break;
                        }

                    }

                    if ($ftTypeID > 0) {
                        $type = Type::getByID($ftTypeID);

                        if ($type instanceof \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type) {
                            $src = $f->getThumbnailURL($type->getBaseVersion());
                            $widthAttribute = $type->getBaseVersion()->getWidth();
                            $heightAttribute = $type->getBaseVersion()->getHeight();
                            // If height is not set thumbnail retains original image proportions
                            if($heightAttribute == 0) {
                                $widthAttribute = $f->getAttribute('width');
                                $heightAttribute = $f->getAttribute('height');
                            }

                            // Note, the above if statement used to also include $width > 0, but this
                            // was making it so that you couldn't use a thumbnail on the extra small screen size.
                            // I removed this part of the conditional and things seem ok ?! even though I would
                            // have thought this could result in double images. Let's keep an eye on this.
                            $sources[] = ['src' => $src, 'width' => $width, 'widthAttribute' => $widthAttribute,  'heightAttribute' => $heightAttribute];
                        }
                    } else {
                        // We're displaying the "full size" image at this breakpoint
                        $sources[] = ['src' => $fallbackSrc, 'widthAttribute' => $f->getAttribute('width'),  'heightAttribute' => $f->getAttribute('height')];
                    }
                }

                $imageTag = Picture::create($sources, $fallbackSrc);
                $imageTag->setAttribute("width", $f->getAttribute('width'));
                $imageTag->setAttribute("height", $f->getAttribute('height'));

                break;

            case "full_size":
                $imageTag->setAttribute("src", $f->getRelativePath());
                $imageTag->setAttribute("width", $f->getAttribute('width'));
                $imageTag->setAttribute("height", $f->getAttribute('height'));

                break;

            case "constrain_size":
                /** @var BasicThumbnailer $im */
                $im = $app->make('helper/image');

                $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);

                $imageTag->setAttribute("src", $thumb->src);
                $imageTag->setAttribute("width", $thumb->width ?? null);
                $imageTag->setAttribute("height", $thumb->height ?? null);

                break;
        }

        // Add image dimensions
        if (empty($imageTag->getAttribute('width')) || empty($imageTag->getAttribute('height'))) {
            $imageTag->setAttribute('width', $f->getAttribute('width'));
            $imageTag->setAttribute('height', $f->getAttribute('height'));
        }
    }

    $imageTag->addClass('ccm-image-block img-fluid bID-' . $bID);

    if ($altText) {
        $imageTag->alt(h($altText));
    } else {
        $imageTag->alt('');
    }

    if ($title) {
        $imageTag->title(h($title));
    }

    // add data attributes for hover effect
    if (is_object($foS) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
        $imageTag->addClass('ccm-image-block-hover');
        $imageTag->setAttribute('data-default-src', $imgPaths['default']);
        $imageTag->setAttribute('data-hover-src', $imgPaths['hover']);
    }

    if (!empty($lazyLoad)) {
        $imageTag->setAttribute('loading', 'lazy');
    }

    if ($linkURL) {
        $linkTag = new Element("a");
        $linkTag->setAttribute("href", $linkURL);
        $linkTag->setChild($imageTag);

        if ($openLinkInNewWindow) {
            $linkTag->setAttribute("target", "_blank");
            $linkTag->setAttribute("rel", "noopener noreferrer");
        }

        echo $linkTag;
    } else {
        echo $imageTag;
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
            var image = images[i];
            image.onmouseover = function () {
                this.setAttribute('src', this.getAttribute('data-hover-src'));
            };
            image.onmouseout = function () {
                this.setAttribute('src', this.getAttribute('data-default-src'));
            };
        }
    </script>
<?php }

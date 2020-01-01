<?php defined('C5_EXECUTE') or die('Access Denied.');
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

if (is_object($f) && $f->getFileID()) {
    if ($f->getTypeObject()->isSVG()) {
        $tag = new \HtmlObject\Image();
        $tag->src($f->getRelativePath());
         if ($maxWidth > 0) {
            $tag->width($maxWidth);
        }
        if ($maxHeight > 0) {
            $tag->height($maxHeight);
        }
        $tag->addClass('ccm-svg');
    } elseif ($maxWidth > 0 || $maxHeight > 0) {
        $im = $app->make('helper/image');
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);

        if ($f->getTypeObject()->isWEBP()) {
            $sources = ['src' => $thumb->src, 'width' => 0];
            $fallbackSrc = false;
            if ($fFallback && is_object($fFallback)) {
                $thumbFallback = $im->getThumbnail($fFallback, $maxWidth, $maxHeight, $cropImage);
                $fallbackSrc = $thumbFallback->src;
            }
            $tag = new \Concrete\Core\Html\Object\Picture($sources, $fallbackSrc);
        } else {
            $tag = new \HtmlObject\Image();
            $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);

        }

    } else {
        if ($f->getTypeObject()->isWEBP()) {
            $sources = ['src' => $f->getRelativePath(), 'width' => 0];
            $fallbackSrc = false;
            if ($fFallback && is_object($fFallback)) {
                $fallbackSrc = $fFallback->getRelativePath();
            }
            $tag = new \Concrete\Core\Html\Object\Picture($sources, $fallbackSrc);
        } else {
            $image = $app->make('html/image', [$f]);
            $tag = $image->getTag();
        }
    }

    $tag->addClass('ccm-image-block img-responsive bID-' . $bID);

    if ($altText) {
        $tag->alt(h($altText));
    } else {
        $tag->alt('');
    }

    if ($title) {
        $tag->title(h($title));
    }

    if ($linkURL) {
        echo '<a href="' . $linkURL . '" '. ($openLinkInNewWindow ? 'target="_blank" rel="noopener noreferrer"' : '') .'>';
    }

    // add data attributes for hover effect
    if (is_object($f) && is_object($foS)) {
        if (($maxWidth > 0 || $maxHeight > 0) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
            if ($tag instanceof \Concrete\Core\Html\Object\Picture) {
                // if foS is webp use it for source only and use fallback for img
                // if foS is NOT webp use it for both source and img
                if ($foS->getTypeObject()->isWEBP()) {
                    foreach ($tag->getChildren() as $child) {
                        if ($child instanceof \HtmlObject\Image) {
                            if (isset($imgPaths['defaultFallback'])
                                && $imgPaths['defaultFallback']
                                && isset($imgPaths['hoverFallback'])
                                && $imgPaths['hoverFallback']
                            ) {
                                $child->setAttribute('data-default-src', $imgPaths['defaultFallback']);
                                $child->setAttribute('data-hover-src', $imgPaths['hoverFallback']);
                            }
                        } else {
                            $child->setAttribute('data-default-src', $imgPaths['default']);
                            $child->setAttribute('data-hover-src', $imgPaths['hover']);
                        }
                    }
                } else {
                    foreach ($tag->getChildren() as $child) {
                        $child->setAttribute('data-default-src', $imgPaths['default']);
                        $child->setAttribute('data-hover-src', $imgPaths['hover']);
                    }
                }
            } else {
                $tag->addClass('ccm-image-block-hover');
                $tag->setAttribute('data-default-src', $imgPaths['default']);
                $tag->setAttribute('data-hover-src', $imgPaths['hover']);
            }
        }
    }

    echo $tag;

    if ($linkURL) {
        echo '</a>';
    }
} elseif ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Image Block.'); ?></div>
<?php
}

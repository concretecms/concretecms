<?php
namespace Concrete\Core\Html;

use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Html\Object\JavaScriptLazyImage;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use HtmlObject\Image as HtmlObjectImage;

class Image
{
    /**
     * @var bool|null if true, this object will return a Picture element instead of an Image element
     */
    protected $usePictureTag = false;

    /**
     * @var \Concrete\Core\Html\Object\Picture|\HtmlObject\Image|\Concrete\Core\Html\Object\JavaScriptLazyImage
     */
    protected $tag;

    /**
     * @var \Concrete\Core\Page\Theme\Theme
     */
    protected $theme;

    /**
     * @param \Concrete\Core\Entity\File\File $f
     * @param array|bool|null $options Use NULL to use the default options, a boolean (deprecated) to use a `picture` tag (TRUE) or an `img` tag (FALSE), or an array with these values:
     * <ul>
     *     <li>bool|null `usePictureTag`: TRUE (use a `picture` tag), FALSE (use an `img` tag), NULL or not specified use the settings from the current page theme</li>
     *     <li>bool|null `lazyLoadNative`: If TRUE the `loading="lazy"` attribute will be added to the `img`</li>
     *     <li>bool|null `lazyLoadJavaScript`: If TRUE set the img `src` and/or source `srcset` image file path to `data-src` and/or `data-srcset`</li>
     *     <li>bool|null `forceAbsoluteURL`: If TRUE set the img `src` will always be absolute; if NULL or not specified, we'll read the </li>
     * </ul>
     */
    public function __construct(File $f = null, $options = null)
    {
        if ($f === null) {
            return false;
        }

        if ($options === null) {
            $options = [];
        } elseif (!is_array($options)) {
            $options = [
                'usePictureTag' => $options,
            ];
        }
        $options += [
            'usePictureTag' => null,
            'lazyLoadNative' => null,
            'lazyLoadJavaScript' => null
        ];

        if ($options['usePictureTag'] !== null) {
            $this->usePictureTag = $options['usePictureTag'];
        } else {
            $this->loadPictureSettingsFromTheme();
        }
        $forceAbsoluteURL = $options['forceAbsoluteURL'] ?? $this->getDefaultForceAbsoluteURL();

        if ($this->usePictureTag) {
            if ($this->theme === null) {
                $c = Page::getCurrentPage();
                $this->theme = $c->getCollectionThemeObject();
            }
            $sources = [];
            $fallbackSrc = $forceAbsoluteURL ? null : $f->getRelativePath();
            if (!$fallbackSrc) {
                $fallbackSrc = $f->getURL();
            }
            foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
                $type = Type::getByHandle($thumbnail);
                if ($type != null) {
                    $src = $f->getThumbnailURL($type->getBaseVersion());
                    $sources[] = ['src' => $src, 'width' => $width];
                    if ($width == 0) {
                        $fallbackSrc = $src;
                    }
                }
            }
            $this->tag = Picture::create($sources, $fallbackSrc, [], $options['lazyLoadNative'], $options['lazyLoadJavaScript']);
        } else {
            $path = $forceAbsoluteURL ? null : $f->getRelativePath();
            if (!$path) {
                $path = $f->getURL();
            }
            if ($options['lazyLoadJavaScript']) {
                // Return a simple img element wrapped in "<noscript></noscript>" and an img element with the
                // image file path set to "data-src". Both img elements have the "loading" attribute optionally set to "lazy".
                $this->tag = JavaScriptLazyImage::create($path, [], $options['lazyLoadNative']);
            } else {
                // Return a simple img element.
                $this->tag = HtmlObjectImage::create($path);
                $this->tag->width((string) $f->getAttribute('width'));
                $this->tag->height((string) $f->getAttribute('height'));

                if ($options['lazyLoadNative']) {
                    $this->tag->loading('lazy');
                }
            }
        }
    }

    /**
     * Returns an object that represents the HTML tag.
     *
     * @see https://github.com/Anahkiasen/html-object
     *
     * @return \Concrete\Core\Html\Object\Picture|\HtmlObject\Image|\Concrete\Core\Html\Object\JavaScriptLazyImage
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Load picture settings from the theme.
     *
     * If the theme uses responsive image maps,
     * the getTag method will return a Picture object.
     */
    protected function loadPictureSettingsFromTheme()
    {
        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $pt = $c->getPageController()->getTheme();
            if (is_object($pt)) {
                $pt = $pt->getThemeHandle();
            }
            $th = Theme::getByHandle($pt);
            if (is_object($th)) {
                $this->theme = $th;
                $this->usePictureTag = count($th->getThemeResponsiveImageMap()) > 0;
            }
        }
    }

    protected function getDefaultForceAbsoluteURL(): bool
    {
        $site = app('site')->getSite();

        return $site ? (bool) $site->getConfigRepository()->get('misc.img_src_absolute') : false;
    }
}

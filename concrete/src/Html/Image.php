<?php
namespace Concrete\Core\Html;

use Concrete\Core\Entity\File\File;
use PageTheme;

class Image
{
    /**
     * @var bool|null If true, this object will return a Picture tag instead of an Image tag.
     */
    protected $usePictureTag = false;

    /**
     * @var \Concrete\Core\Html\Object\Picture|\HTMLObject\Image
     */
    protected $tag;

    /**
     * @var \Concrete\Core\Page\Theme\Theme
     */
    protected $theme;

    /**
     * @param \Concrete\Core\Entity\File $f
     * @param bool|null $usePictureTag
     */
    public function __construct(File $f = null, $usePictureTag = null)
    {
        if (!is_object($f)) {
            return false;
        }

        if (isset($usePictureTag)) {
            $this->usePictureTag = $usePictureTag;
        } else {
            $this->loadPictureSettingsFromTheme();
        }

        if ($this->usePictureTag) {
            if (!isset($this->theme)) {
                $c = \Page::getCurrentPage();
                $this->theme = $c->getCollectionThemeObject();
            }
            $sources = array();
            $fallbackSrc = $f->getRelativePath();
            if (!$fallbackSrc) {
                $fallbackSrc = $f->getURL();
            }
            foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
                $type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle($thumbnail);
                if ($type != null) {
                    $src = $f->getThumbnailURL($type->getBaseVersion());
                    $sources[] = array('src' => $src, 'width' => $width);
                    if ($width == 0) {
                        $fallbackSrc = $src;
                    }
                }
            }
            $this->tag = \Concrete\Core\Html\Object\Picture::create($sources, $fallbackSrc);
        } else {
            // Return a simple image tag.
            $path = $f->getRelativePath();
            if (!$path) {
                $path = $f->getURL();
            }
            $this->tag = \HtmlObject\Image::create($path);
            $this->tag->width((string) $f->getAttribute('width'));
            $this->tag->height((string) $f->getAttribute('height'));
        }
    }

    /**
     * Returns an object that represents the HTML tag.
     *
     * @return \Concrete\Core\Html\Object\Picture|\HTMLObject\Image
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
        $c = \Page::getCurrentPage();
        if (is_object($c)) {
            $pt = $c->getPageController()->getTheme();
            if (is_object($pt)) {
                $pt = $pt->getThemeHandle();
            }
            $th = PageTheme::getByHandle($pt);
            if (is_object($th)) {
                $this->theme = $th;
                $this->usePictureTag = count($th->getThemeResponsiveImageMap()) > 0;
            }
        }
    }
}

<?php
namespace Concrete\Core\Html\Object;

use HtmlObject\Element;
use HtmlObject\Image;

class JavaScriptLazyImage extends Element
{
    /**
     * Whether the element is self closing.
     *
     * @var bool
     */
    protected $isSelfClosing = false;

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// CORE METHODS //////////////////////////
    ////////////////////////////////////////////////////////////////////

    public function __construct($src, $attributes = array(), $lazyLoadNative = false)
    {
        $this->noscriptFallback($src, $lazyLoadNative);
        $this->img($src, $lazyLoadNative);
    }

    /**
     * Static alias for constructor.
     *
     * @param string|null $src
     * @param array $attributes
     * @param bool|null $lazyLoadNative
     *
     * @return \Concrete\Core\Html\Object\JavaScriptLazyImage
     */
    public static function create($src = false, $attributes = array(), $lazyLoadNative = false)
    {
        return new static($src, $attributes, $lazyLoadNative);
    }

    /**
     * Create an img element wrapped in "<noscript></noscript>". The image will be displayed if JavaScript is disabled.
     *
     * Example output:
     * <noscript>
     *     <img src="...">
     * </noscript>
     * or
     * <noscript>
     *     <img src="..." loading="lazy">
     * </noscript>
     *
     * @param string $src The file path of an image.
     * @param bool $lazyLoadNative If true, the image "loading" attribute is set to "lazy".
     *
     * @return \Concrete\Core\Html\Object\JavaScriptLazyImage
     */
    public function noscriptFallback($src, $lazyLoadNative = false)
    {
        $this->nest('<noscript>');

        $img = Image::create();
        $img->src($src);

        if ($lazyLoadNative) {
            $img->loading('lazy');
        }

        $this->setChild($img);

        $this->nest('</noscript>');

        return $this;
    }

    /**
     * Create an image element with the image path set to a "data-src" attribute.
     *
     * Example output:
     * <img data-src="...">
     * or
     * <img data-src="..." loading="lazy">
     *
     * @param string $src The file path of an image.
     * @param bool $lazyLoadNative If true, the image "loading" attribute is set to "lazy".
     */
    public function img($src, $lazyLoadNative = false)
    {
        $img = Image::create();
        $img->src(false);
        $img->dataSrc($src);

        if ($lazyLoadNative) {
            $img->loading('lazy');
        }

        $this->setChild($img);
    }

    /**
     * Set the image and noscript image fallback "alt" attribute value.
     *
     * @param string $alt
     */
    public function alt($alt)
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Image) {
                $child->alt($alt);
            }
        }
    }

    /**
     * Set the image and noscript image fallback "title" attribute value.
     *
     * @param string $title
     */
    public function title($title)
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Image) {
                $child->title($title);
            }
        }
    }

    /**
     * Add one or more CSS classes to the image and noscript image fallback.
     *
     * @param string $classes A string of space separated CSS classes.
     */
    public function addClass($classes)
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Image) {
                $child->addClass($classes);
            }
        }
    }
}

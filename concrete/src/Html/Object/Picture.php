<?php

namespace Concrete\Core\Html\Object;

use HtmlObject\Element;
use HtmlObject\Image;

class Picture extends Element
{
    /**
     * Default element.
     *
     * @var string
     */
    protected $element = 'picture';

    /**
     * Whether the element is self closing.
     *
     * @var bool
     */
    protected $isSelfClosing = false;

    /**
     * Default element for nested children.
     *
     * @var string
     */
    protected $defaultChild = 'source';

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// CORE METHODS //////////////////////////
    ////////////////////////////////////////////////////////////////////

    public function __construct(array $sources = [], $fallbackSrc, $attributes = [], $lazyLoadNative = false, $lazyLoadJavaScript = false)
    {
        $this->sources($sources, $lazyLoadJavaScript);

        if ($lazyLoadJavaScript) {
            $this->noscriptFallback($fallbackSrc, $lazyLoadNative);
        }

        $this->fallback($fallbackSrc, $lazyLoadNative, $lazyLoadJavaScript);
    }

    /**
     * Static alias for constructor.
     *
     * @param array $sources
     * @param string|null $fallbackSrc
     * @param array $attributes
     * @param bool|null $lazyLoadNative
     * @param bool|null $lazyLoadJavaScript
     *
     * @return \Concrete\Core\Html\Object\Picture
     */
    public static function create($sources = [], $fallbackSrc = false, $attributes = [], $lazyLoadNative = false, $lazyLoadJavaScript = false)
    {
        return new static($sources, $fallbackSrc, $attributes, $lazyLoadNative, $lazyLoadJavaScript);
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// CHILDREN ///////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Create the source elements for the picture element.
     *
     * Example output:
     * <!--[if IE 9]><video style='display: none;'><![endif]-->
     * <source srcset="..." media="(min-width: ...)">
     * <source srcset="..." media="(min-width: ...)">
     * <source srcset="...">
     * <!--[if IE 9]></video><![endif]-->
     * or
     * <!--[if IE 9]><video style='display: none;'><![endif]-->
     * <source data-srcset="..." media="(min-width: ...)">
     * <source data-srcset="..." media="(min-width: ...)">
     * <source data-srcset="...">
     * <!--[if IE 9]></video><![endif]-->
     *
     * @param array $sources an array of image thumbnail file paths
     * @param bool $lazyLoadJavaScript if true, the source image thumbnail file path is set to a "data-srcset" attribute instead of "srcset"
     *
     * @return \Concrete\Core\Html\Object\Picture
     */
    public function sources($sources, $lazyLoadJavaScript = false)
    {
        $this->nest("<!--[if IE 9]><video style='display: none;'><![endif]-->");

        foreach ($sources as $source) {
            $path = $source['src'];
            $width = $source['width'];
            $source = Source::create();

            if ($lazyLoadJavaScript) {
                $source->dataSrcset($path);
            } else {
                $source->srcset($path);
            }

            if ($width != 0) {
                $source->media("(min-width: $width)");
            }
            $this->setChild($source);
        }

        $this->nest('<!--[if IE 9]></video><![endif]-->');

        return $this;
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
     * @param string $src the file path of an image
     * @param bool $lazyLoadNative if true, the image "loading" attribute is set to "lazy"
     *
     * @return \Concrete\Core\Html\Object\Picture
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
     * Create the img element fallback for the picture element.
     *
     * Example output:
     * <img src="...">
     * or
     * <img src="..." loading="lazy">
     * or
     * <img data-src="..." loading="lazy">
     * or
     * <img data-src="...">
     *
     * @param string $src the file path of an image
     * @param bool $lazyLoadNative if true, the image "loading" attribute is set to "lazy"
     * @param bool $lazyLoadJavaScript if true, the image path is set to a "data-src" attribute
     */
    public function fallback($src, $lazyLoadNative = false, $lazyLoadJavaScript = false)
    {
        $img = Image::create();
        $img->src($src);

        if ($lazyLoadNative) {
            $img->loading('lazy');
        }

        if ($lazyLoadJavaScript) {
            $img->src(false);
            $img->dataSrc($src);
        }

        $this->setChild($img);
    }

    /**
     * Set the image fallback and noscript image fallback "alt" attribute value.
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
     * Set the image fallback and noscript image fallback "title" attribute value.
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
     * @param string $classes a string of space separated CSS classes
     */
    public function addClass($classes)
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Image) {
                $child->addClass($classes);
            }
        }
    }

    /**
     * Set an attribute.
     *
     * @param string $attribute
     * @param string|null $value
     */
    public function setAttribute($attribute, $value = null)
    {
        foreach ($this->getChildren() as $child) {
            if ($child instanceof Image) {
                $child->setAttribute($attribute, $value);
            }
        }
    }
}

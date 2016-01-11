<?php
namespace Concrete\Core\Html\Object;

use HtmlObject\Element;

class HeadLink extends Element
{
    /**
     * Default element.
     *
     * @var string
     */
    protected $element = 'link';

    /**
     * Whether the element is self closing.
     *
     * @var bool
     */
    protected $isSelfClosing = true;

    /**
     * Create a new Link.
     *
     * @param string $href  Link url
     * @param string $rel   Link relation (stylesheet)
     * @param string $type  Link type (text/css)
     * @param string $media Link media (screen, print, etc)
     *
     * @return HeadLink
     */
    public function __construct($href = '#', $rel = null, $type = null, $media = null)
    {
        $attributes = array();
        foreach (array('href', 'rel', 'type', 'media') as $k) {
            if (!is_null($$k)) {
                $attributes[$k] = $$k;
            }
        }
        $this->setTag('link', null, $attributes);
    }

    /**
     * Static alias for constructor.
     *
     * @param string $href  Link url
     * @param string $rel   Link relation (stylesheet)
     * @param string $type  Link type (text/css)
     * @param string $media Link media (screen, print, etc)
     *
     * @return HeadLink
     */
    public static function create($href = '#', $rel = null, $type = null, $media = null)
    {
        return new static($href, $rel, $type, $media);
    }
}

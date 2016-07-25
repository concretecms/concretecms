<?php
namespace Concrete\Core\Html\Object;

use HtmlObject\Element;

class Source extends Element
{
    /**
     * Default element.
     *
     * @var string
     */
    protected $element = 'source';

    /**
     * Whether the element is self closing.
     *
     * @var bool
     */
    protected $isSelfClosing = true;

    public function __construct()
    {
    }
}

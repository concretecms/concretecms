<?php

namespace Concrete\Core\Area\Layout\Formatter;

use Concrete\Core\Area\Layout\Layout;
use HtmlObject\Element;
use Sunra\PhpSimple\HtmlDomParser;

class ThemeGridFormatter implements FormatterInterface
{

    protected $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getLayoutContainerHtmlObject()
    {


        $gf = $this->layout->getThemeGridFrameworkObject();
        if (is_object($gf)) {
            $dom = new HtmlDomParser();
            $r = $dom->str_get_html(
                $gf->getPageThemeGridFrameworkRowStartHTML() .
                $gf->getPageThemeGridFrameworkRowEndHTML()
            );

            if (is_object($r)) {
                $nodes = $r->childNodes();
                $node = $nodes[0];

                $element = new Element($node->tag);
                $element->id($node->id);
                $element->class($node->class);
            } else {
                $element = new Element('div');
            }
            return $element;

        }
    }



}
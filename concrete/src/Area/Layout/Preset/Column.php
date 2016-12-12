<?php
namespace Concrete\Core\Area\Layout\Preset;

use Concrete\Core\Area\Layout\ColumnInterface;
use HtmlObject\Element;
use Sunra\PhpSimple\HtmlDomParser;

class Column implements ColumnInterface
{

    protected $column;

    public static function fromHtml($html)
    {
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($html);

        $nodes = $r->childNodes();
        $node = $nodes[0];

        $element = new Element($node->tag);
        foreach($node->getAllAttributes() as $key => $value) {
            $element->setAttribute($key, $value);
        }

        $column = new static($element);
        return $column;
    }

    public function __construct(Element $column)
    {
        $this->column = $column;
    }

    public function getColumnHtmlObject()
    {
        return $this->column;
    }

    public function getColumnHtmlObjectEditMode()
    {
        $column = $this->getColumnHtmlObject();
        $inner = new Element('div');
        $inner->addClass('ccm-layout-column-inner ccm-layout-column-highlight');
        $column->appendChild($inner);
        return $column;
    }

}
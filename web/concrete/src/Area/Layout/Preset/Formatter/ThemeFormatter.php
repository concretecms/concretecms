<?php
namespace Concrete\Core\Area\Layout\Preset\Formatter;


use Concrete\Core\Area\Layout\Preset\Preset;
use HtmlObject\Element;
use Sunra\PhpSimple\HtmlDomParser;

class ThemeFormatter implements FormatterInterface
{

    protected $arrayPreset;

    public function __construct($arrayPreset)
    {
        $this->arrayPreset = $arrayPreset;
    }

    public function getPresetContainerHtmlObject()
    {
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($this->arrayPreset['container']);
        if (is_object($r)) {
            $nodes = $r->childNodes();
            $node = $nodes[0];

            if (is_object($node)) {
                $element = new Element($node->tag);
                foreach($node->getAllAttributes() as $key => $value) {
                    $element->setAttribute($key, $value);
                }
            }
        }

        if (!isset($element)) {
           $element = new Element('div');
        }

        return $element;
    }
}
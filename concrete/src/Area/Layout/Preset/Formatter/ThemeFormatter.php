<?php
namespace Concrete\Core\Area\Layout\Preset\Formatter;

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
                $root = $this->getElementForNode($node);

                $children = $node->children();
                $element = $root;
                while (count($children) > 0) {
                    $node = $children[0];

                    $parent = $element;
                    $element = $this->getElementForNode($node);
                    $parent->appendChild($element);
                    $children = $node->children();
                }
            }
        }

        if (!isset($root)) {
            $root = '';
        }

        return $root;
    }

    private function getElementForNode($node) {
        $element = new Element($node->tag);
        foreach ($node->getAllAttributes() as $key => $value) {
            $element->setAttribute($key, $value);
        }
        return $element;
    }
}

<?php
namespace Concrete\Core\StyleCustomizer;

use Core;

class StyleListParser
{
    protected $root;

    public function __construct(\SimpleXMLElement $root)
    {
        $this->root = $root;
    }

    public function parse()
    {
        $sl = new StyleList();
        foreach ($this->root->set as $xset) {
            $set = $sl->addSet((string) $xset['name']);
            foreach ($xset->style as $xstyle) {
                $type = camelcase((string) $xstyle['type']);
                $style = Core::make('\\Concrete\\Core\\StyleCustomizer\\Style\\' . $type . 'Style');
                $style->setName((string) $xstyle['name']);
                $style->setVariable((string) $xstyle['variable']);
                $set->addStyle($style);
            }
        }

        return $sl;
    }
}

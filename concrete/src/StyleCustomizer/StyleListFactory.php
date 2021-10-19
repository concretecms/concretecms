<?php
namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\Parser\ParserManager;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\ManagerInterface;

class StyleListFactory
{

    public function createStyleList(ManagerInterface $parserManager, \SimpleXMLElement $root, PresetInterface $preset)
    {
        $sl = new StyleList();
        foreach ($root->set as $setNode) {
            $set = $sl->addSet((string) $setNode['name']);
            foreach ($setNode->style as $styleNode) {
                $parser = $parserManager->getParserFromType((string) $styleNode['type']);
                $style = $parser->parseNode($styleNode, $preset);
                $set->addStyle($style);
            }
        }

        return $sl;
    }
}

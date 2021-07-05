<?php
namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\StyleCustomizer\Style\Parser\ParserManager;

class StyleListParser
{
    /**
     * @var ParserManager
     */
    protected $manager;

    public function __construct(ParserManager $manager)
    {
        $this->manager = $manager;
    }

    public function parse(\SimpleXMLElement $root)
    {
        $sl = new StyleList();
        foreach ($root->set as $setNode) {
            $set = $sl->addSet((string) $setNode['name']);
            foreach ($setNode->style as $styleNode) {
                $parser = $this->manager->driver((string) $styleNode['type']);
                $style = $parser->parseNode($styleNode);
                $set->addStyle($style);
            }
        }

        return $sl;
    }
}

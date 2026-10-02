<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;
use Concrete\Core\StyleCustomizer\Style\TypeStyle;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;

class TypeParser extends AbstractParser
{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Parser\AbstractParser::createStyleObject()
     *
     * @return \Concrete\Core\StyleCustomizer\Style\TypeStyle
     */
    public function createStyleObject(): StyleInterface
    {
        return new TypeStyle();
    }

    /**
     * @var WebFontCollectionFactory
     */
    protected $webFontCollectionFactory;

    /**
     * @param WebFontCollectionFactory $webFontCollectionFactory
     */
    public function __construct(WebFontCollectionFactory $webFontCollectionFactory)
    {
        $this->webFontCollectionFactory = $webFontCollectionFactory;
    }

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): StyleInterface
    {
        $collection = $this->webFontCollectionFactory->createFromPreset($preset);
        /** @var \Concrete\Core\StyleCustomizer\Style\TypeStyle $style */
        $style = parent::parseNode($element, $preset);
        $style->setWebFonts($collection);
        return $style;
    }

}
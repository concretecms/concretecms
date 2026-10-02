<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\FontFamilyStyle;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleInterface;

class FontFamilyParser extends AbstractParser
{

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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Parser\AbstractParser::createStyleObject()
     *
     * @return \Concrete\Core\StyleCustomizer\Style\FontFamilyStyle
     */
    public function createStyleObject(): StyleInterface
    {
        return new FontFamilyStyle();
    }


    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): StyleInterface
    {
        $collection = $this->webFontCollectionFactory->createFromPreset($preset);
        /** @var \Concrete\Core\StyleCustomizer\Style\FontFamilyStyle $style */
        $style = parent::parseNode($element, $preset);
        $style->setWebFonts($collection);
        return $style;
    }

}
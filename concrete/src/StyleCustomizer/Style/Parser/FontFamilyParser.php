<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Preset\PresetInterface;
use Concrete\Core\StyleCustomizer\Style\FontFamilyStyle;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;

class FontFamilyParser implements ParserInterface
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

    public function parseNode(\SimpleXMLElement $element, PresetInterface $preset): Style
    {
        $style = new FontFamilyStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        $style->setWebFonts($this->webFontCollectionFactory->createFromPreset($preset));
        return $style;
    }

}
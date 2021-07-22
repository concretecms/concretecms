<?php

namespace Concrete\Core\StyleCustomizer\Style\Parser;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\TypeStyle;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;

class TypeParser implements ParserInterface
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

    public function parseNode(\SimpleXMLElement $element, SkinInterface $skin): Style
    {
        $collection = $this->webFontCollectionFactory->createFromSkin($skin);
        $style = new TypeStyle();
        $style->setName((string) $element['name']);
        $style->setVariable((string) $element['variable']);
        $style->setWebFonts($collection);
        return $style;
    }

}
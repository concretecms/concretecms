<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\WebFont\WebFontCollection;

trait WebFontCollectionStyleTrait
{

    /**
     * @var WebFontCollection
     */
    protected $webFonts;

    /**
     * @return WebFontCollection
     */
    public function getWebFonts(): ?WebFontCollection
    {
        return $this->webFonts;
    }

    /**
     * @param WebFontCollection $webFonts
     */
    public function setWebFonts(WebFontCollection $webFonts): void
    {
        $this->webFonts = $webFonts;
    }




}

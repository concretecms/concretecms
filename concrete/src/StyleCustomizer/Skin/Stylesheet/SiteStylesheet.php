<?php

namespace Concrete\Core\StyleCustomizer\Skin\Stylesheet;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use HtmlObject\Element;

class SiteStylesheet implements StylesheetInterface
{

    /**
     * @var Site
     */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getElement(): Element
    {
        $skinIdentifier = $this->site->getThemeSkinIdentifier();
        if (!$skinIdentifier) {
            $skinIdentifier = SkinInterface::SKIN_DEFAULT;
        }
        $skinIdentifierDark = $this->site->getThemeSkinIdentifierDark();
        $theme = Theme::getByID($this->site->getThemeID());
        $skinDark = null;
        $skinLight = null;
        if ($theme) {
            $skinLight = $theme->getSkinByIdentifier($skinIdentifier);
            if ($skinIdentifierDark) {
                $skinDark = $theme->getSkinByIdentifier($skinIdentifierDark);
            }
        }
        if ($skinLight instanceof SkinInterface) {
            $skinLightStylesheet = $skinLight->getStylesheet();
            if ($skinDark instanceof SkinInterface) {
                $skinDarkStylesheet = $skinDark->getStylesheet();

                $lightHref = $skinLightStylesheet->getElement()->getAttributes()['href'];
                $darkHref = $skinDarkStylesheet->getElement()->getAttributes()['href'];

                $lightElement = new Element('link', null);
                $lightElement->setIsSelfClosing(true);
                $lightElement->rel('stylesheet')->type('text/css')->href($lightHref);
                $lightElement->media('(prefers-color-scheme: light)');

                $darkElement = new Element('link', null);
                $darkElement->setIsSelfClosing(true);
                $darkElement->rel('stylesheet')->type('text/css')->href($darkHref);
                $darkElement->media('(prefers-color-scheme: dark)');

                $elementWrapper = new Element(null, null);
                $elementWrapper->appendChild($lightElement);
                $elementWrapper->appendChild($darkElement);
                return $elementWrapper;
            }
            return $skinLightStylesheet->getElement();
        }

        return new Element(); // Have to return something.
    }

}

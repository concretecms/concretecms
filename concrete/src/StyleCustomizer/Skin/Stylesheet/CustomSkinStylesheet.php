<?php

namespace Concrete\Core\StyleCustomizer\Skin\Stylesheet;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use HtmlObject\Element;

class CustomSkinStylesheet implements StylesheetInterface
{

    /**
     * @var SkinInterface
     */
    protected $skin;

    public function __construct(SkinInterface $skin)
    {
        $this->skin = $skin;
    }

    public function getElement(): Element
    {
        $config = app('config');
        $noCacheValue = "?ccm_nocache=" . sha1($noCacheValue = $config->get('concrete.version_installed') . '-' . $config->get('concrete.version_db') . "-" . $config->get('concrete.cache.last_cleared'));
        $stylesheet = REL_DIR_FILES_UPLOADED_STANDARD . '/' . DIRNAME_STYLE_CUSTOMIZER_PRESETS . '/' . $this->skin->getIdentifier() . '.css';
        $element = new Element('link', null);
        $element->setIsSelfClosing(true);
        $element->rel('stylesheet')->type('text/css')->href($stylesheet . $noCacheValue);
        return $element;
    }

}

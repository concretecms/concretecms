<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Page;
use Core;

class UrlSlugCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('url_slug');
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/text/icon.png');
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'URL Slug');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $this->addPageTypeComposerControlRequestValue('cHandle', $data['url_slug']);
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $e = Loader::helper('validation/error');
        $handle = $this->getPageTypeComposerControlDraftValue();
        
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if (!$stringValidator->notempty($handle)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));
            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionHandle();
        }
    }

}

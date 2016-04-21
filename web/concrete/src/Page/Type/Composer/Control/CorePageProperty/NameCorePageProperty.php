<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Core;
use Page;

class NameCorePageProperty extends CorePageProperty
{
    protected $ptComposerControlRequiredByDefault = true;

    public function __construct()
    {
        $this->setCorePagePropertyHandle('name');
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/text/icon.png');
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Page Name');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $slug = array_filter($controls, function ($item) {
            if ($item instanceof UrlSlugCorePageProperty) {
                return true;
            }

            return false;
        });
        $this->addPageTypeComposerControlRequestValue('cName', $data['name']);
        if (!count($slug) && $c->isPageDraft()) {
            $txt = new \URLify();
            $this->addPageTypeComposerControlRequestValue('cHandle', $txt->filter($data['name']));
        }
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $e = Core::make('helper/validation/error');
        $val = $this->getRequestValue();
        if ($val['name']) {
            $name = $val['name'];
        } else {
            $name = $this->getPageTypeComposerControlDraftValue();
        }
        
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if (!$stringValidator->notempty($name)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));

            return $e;
        }
    }

    public function getRequestValue($args = false)
    {
        $data = parent::getRequestValue($args);
        $data['name'] = Core::make('helper/security')->sanitizeString($data['name']);

        return $data;
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionName();
        }
    }
}

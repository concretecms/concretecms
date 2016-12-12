<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Concrete\Core\Page\Page;
use Core;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class UrlSlugCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('url_slug');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('file-text'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'URL Slug');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $data += [
            'url_slug' => null,
        ];
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

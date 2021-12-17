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
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('file-alt'));
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
        $val = $this->getRequestValue();
        if (isset($val['url_slug'])) {
            $urlSlug = $val['url_slug'];
        } else {
            $urlSlug = $this->getPageTypeComposerControlDraftValue();
        }

        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if (!$stringValidator->notempty($urlSlug)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));

            return $e;
        }
    }

    public function getRequestValue($args = false)
    {
        $data = parent::getRequestValue($args);
        if(isset($data['url_slug'])) {
            $data['url_slug'] = Core::make('helper/security')->sanitizeString($data['url_slug']);
        }

        return $data;
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionHandle();
        }
    }
}

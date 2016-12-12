<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Concrete\Core\Page\Page;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class PageTemplateCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('page_template');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('list-alt'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Page Template');
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return false;
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $this->addPageTypeComposerControlRequestValue('pTemplateID', \Request::post('ptComposerPageTemplateID'));
        parent::publishToPage($c, $data, $controls);
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getPageTemplateID();
        }
    }
}

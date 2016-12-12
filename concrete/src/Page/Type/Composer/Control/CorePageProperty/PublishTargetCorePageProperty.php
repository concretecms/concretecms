<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class PublishTargetCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('publish_target');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('download'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Page Location');
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return false;
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            if ($this->page->isPageDraft()) {
                return $this->page->getPageDraftTargetParentPageID();
            } else {
                return $this->page->getCollectionParentID();
            }
        } elseif ($this->getTargetParentPageID()) {
            return $this->getTargetParentPageID();
        }
    }

    public function render($label, $customTemplate, $description)
    {
        {
            if (!is_object($this->page) || $this->page->isPageDraft()) {
                parent::render($label, $customTemplate, $description);
            }
        }
    }
}

<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

class PublishTargetCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('publish_target');
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/image_file/icon.png');
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
        } else if ($this->getTargetParentPageID()) {
            return $this->getTargetParentPageID();
        }
    }

    public function render($label, $customTemplate, $description) {
        {
            if (!is_object($this->page) || $this->page->isPageDraft()) {
                parent::render($label, $customTemplate, $description);
            }
        }
    }

}

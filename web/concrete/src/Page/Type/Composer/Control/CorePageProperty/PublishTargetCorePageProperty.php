<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

class PublishTargetCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('publish_target');
        $this->setPageTypeComposerControlName(tc('PageTypeComposerControlName', 'Page Location'));
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/image_file/icon.png');
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

}

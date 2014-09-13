<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Page;

class PageTemplateCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('page_template');
        $this->setPageTypeComposerControlName(tc('PageTypeComposerControlName', 'Page Template'));
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/select/icon.png');
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return false;
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $this->addPageTypeComposerControlRequestValue('pTemplateID', $_POST['ptComposerPageTemplateID']);
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

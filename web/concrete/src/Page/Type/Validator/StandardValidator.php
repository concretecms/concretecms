<?php
namespace Concrete\Core\Page\Type\Validator;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Page\Type\Type;
use Core;

class StandardValidator implements ValidatorInterface
{
    public function setPageTypeObject(Type $type)
    {
        $this->type = $type;
    }

    public function getPageTypeObject()
    {
        return $this->type;
    }

    public function validateCreateDraftRequest($template)
    {
        $e = Core::make('error');
        $availablePageTemplates = $this->type->getPageTypePageTemplateObjects();
        $availablePageTemplateIDs = array();
        foreach ($availablePageTemplates as $ppt) {
            $availablePageTemplateIDs[] = $ppt->getPageTemplateID();
        }
        if (!is_object($template)) {
            $e->add(t('You must choose a page template.'));
        } else {
            if (!in_array($template->getPageTemplateID(), $availablePageTemplateIDs)) {
                $e->add(t('This page template is not a valid template for this page type.'));
            }
        }
        return $e;
    }

    public function validatePublishDraftRequest(Page $page = null)
    {
        $e = Core::make('error');
        $controls = Control::getList($this->type);
        foreach ($controls as $oc) {
            if (is_object($page)) {
                $oc->setPageObject($page);
            }
            if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
                $r = $oc->validate();
                if ($r instanceof \Concrete\Core\Error\Error) {
                    $e->add($r);
                }
            }
        }
        return $e;
    }
}

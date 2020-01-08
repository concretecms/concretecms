<?php

namespace Concrete\Core\Page\Type\Validator;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Page\Type\Composer\Control\CorePageProperty\CorePageProperty;
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
        $availablePageTemplateIDs = [];
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

    public function validatePublishLocationRequest(Page $target = null, Page $page = null)
    {
        $e = Core::make('error');
        if (!is_object($target) || $target->isError()) {
            if (!is_object($page) || !$page->isHomePage()) {
                $e->add(t('You must choose a page to publish this page beneath.'));
            }
        } else {
            $ppc = new \Permissions($target);
            if (!$ppc->canAddSubCollection($this->getPageTypeObject())) {
                $e->add(t('You do not have permission to publish a page in this location.'));
            }
        }

        return $e;
    }

    public function validatePublishDraftRequest(Page $page = null)
    {
        $e = Core::make('error');
        $controls = Control::getList($this->type);
        foreach ($controls as $oc) {
            $validate = false;
            $r = null;
            if (is_object($page)) {
                $oc->setPageObject($page);
            }
            if ($oc instanceof CorePageProperty || $oc instanceof BlockControl) {
                if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
                    $validate = true;
                } else {
                    $validate = false;
                }
            } else {
                // We always want to validate attributes because sometimes attributes can check if their data
                // is in the right format. They then can complain if they are missing certain things, but if the
                // entire field is missing, they throw a field not present error. This error is ignored if the field
                // is not marked as required in the form.
                $validate = true;
            }

            if ($validate) {
                $r = $oc->validate();
            }

            if ($r instanceof ErrorList) {
                $e->add($r);
            }
        }

        return $e;
    }
}

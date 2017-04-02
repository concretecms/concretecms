<?php
namespace Concrete\Controller\Panel\Detail\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Form\Service\Widget\DateTime;
use PageEditResponse;
use PageType;
use View;
use Loader;
use PageTemplate;
use User;
use Core;

class Composer extends BackendInterfacePageController
{
    protected $viewPath = '/panels/details/page/composer';

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }

    public function view()
    {
        $this->requireAsset('javascript', 'core/composer-save-coordinator');
        $pagetype = PageType::getByID($this->page->getPageTypeID());
        $id = $this->page->getCollectionID();
        $saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
        $viewURL = View::url('/dashboard/composer/write', 'draft', $id);
        $this->set('saveURL', $saveURL);
        $this->set('viewURL', $viewURL);
        $this->set('pagetype', $pagetype);
    }

    public function autosave()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            if (!$ptr->error->has()) {
                $ptr->setMessage(t('Page saved on %s', Core::make('helper/date')->formatDateTime($ptr->time, true, true)));
            }
            $ptr->outputJSON();
        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }

    public function saveAndExit()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            $u = new User();
            $c = \Page::getByID($u->getPreviousFrontendPageID());
            $ptr->setRedirectURL($c->getCollectionLink(true));
            $ptr->outputJSON();
        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }

    public function publish()
    {
        if ($this->validateAction()) {
            $r = $this->save();
            $ptr = $r[0];
            $pagetype = $r[1];
            $outputControls = $r[2];

            $c = $this->page;
            $e = $ptr->error;
            $validator = $pagetype->getPageTypeValidatorObject();
            if ($this->page->isPageDraft()) {
                $target = \Page::getByID($this->page->getPageDraftTargetParentPageID());
            } else {
                $target = \Page::getByID($this->page->getCollectionParentID());
            }
            $e->add($validator->validatePublishLocationRequest($target, $c));
            $e->add($validator->validatePublishDraftRequest($c));

            $ptr->setError($e);

            if (!$e->has()) {
                $publishDateTime = false;
                if ($this->request->request->get('action') == 'schedule') {
                    $dateTime = new DateTime();
                    $publishDateTime = $dateTime->translate('check-in-scheduler');
                }

                $pagetype->publish($c, $publishDateTime);
                $ptr->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($c));
            }
            $ptr->outputJSON();
        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }

    public function discard()
    {
        if ($this->validateAction()) {
            $ptr = new PageEditResponse();
            if ($this->permissions->canDeletePage() && $this->page->isPageDraft()) {
                $this->page->delete();
                $u = new User();
                $cID = $u->getPreviousFrontendPageID();
                $ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
            } else {
                $e = Loader::helper('validation/error');
                $e->add(t('You do not have permission to discard this page.'));
                $ptr->setError($e);
            }

            $ptr->outputJSON();
        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }

    protected function save()
    {
        $c = $this->page;
        $ptr = new PageEditResponse();
        $ptr->setPage($c);

        $pagetype = $c->getPageTypeObject();
        $pt = null;
        $ptComposerPageTemplateID = (int) $this->request->post('ptComposerPageTemplateID');
        if ($ptComposerPageTemplateID !== 0) {
            $pt = PageTemplate::getByID($ptComposerPageTemplateID);
        }
        if ($pt === null) {
            $pt = $pagetype->getPageTypeDefaultPageTemplateObject();
        }
        $validator = $pagetype->getPageTypeValidatorObject();
        $e = $validator->validateCreateDraftRequest($pt);
        $outputControls = [];
        if (!$e->has()) {
            $c = $c->getVersionToModify();
            $this->page = $c;

            if ($c->isPageDraft()) {
                /// set the target
                $configuredTarget = $pagetype->getPageTypePublishTargetObject();
                $targetPageID = (int) $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
                if ($targetPageID === 0) {
                    $targetPageID = (int) $this->request->post('cParentID');
                    if ($targetPageID === 0) {
                        $targetPageID = $c->getPageDraftTargetParentPageID();
                    }
                }

                $c->setPageDraftTargetParentPageID($targetPageID);
            }

            $saver = $pagetype->getPageTypeSaverObject();
            $outputControls = $saver->saveForm($c);
        }
        $ptr->setError($e);

        return [$ptr, $pagetype, $outputControls];
    }
}

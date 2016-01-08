<?php
namespace Concrete\Controller\Dialog\Page\Add;
use \Concrete\Core\Controller\Controller;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\View\DialogView;
use Page;
use Permissions;
use Core;
class Compose extends Controller
{

    protected $controllerActionPath = '/ccm/system/dialogs/page/add/compose';

	public function view($ptID, $cParentID)
    {

        $pagetype = Type::getByID($ptID);
        $e = Core::make('error');
        if (is_object($pagetype)) {
            $ptp = new Permissions($pagetype);
            if (!$ptp->canAddPageType()) {
                $e->add(t('You do not have permission to add a page of this type.'));
            }
        } else {
            $e->add(t('Invalid page type.'));
        }

        $parent = Page::getByID($cParentID);
        if (!is_object($parent) || $parent->isError()) {
            $e->add(t('Invalid parent page.'));
        }

        if (!$e->has()) {
            $this->view = new DialogView('/dialogs/page/add/compose');
            $this->set('parent', $parent);
            $this->set('pagetype', $pagetype);
        } else {
            $pr = new EditResponse();
            $pr->setError($e);
            $pr->outputJSON();
        }

        if (!$this->view) {
            throw new \Exception(t('Access Denied.'));
        }
	}

    public function submit()
    {
        $e = Core::make('error');
        $pagetype = Type::getByID($this->request->request->get('ptID'));
        if (is_object($pagetype)) {
            $configuredTarget = $pagetype->getPageTypePublishTargetObject();
            $cParentID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
            if (!$cParentID) {
                $cParentID = $this->request->request->get('cParentID');
            }
        }
        $parent = Page::getByID($cParentID);

        if ($this->request->request->get('ptComposerPageTemplateID')) {
            $template = Template::getByID($this->request->request->get('ptComposerPageTemplateID'));
        }
        if (!is_object($template)) {
            $template = $pagetype->getPageTypeDefaultPageTemplateObject();
        }

        if (is_object($pagetype)) {
            $validator = $pagetype->getPageTypeValidatorObject();
            $e->add($validator->validateCreateDraftRequest($template));
            $e->add($validator->validatePublishLocationRequest($parent));
            if ($this->request->request('addPageComposeAction') == 'publish') {
                $e->add($validator->validatePublishDraftRequest());
            }
        }
        $pr = new EditResponse();
        $pr->setError($e);

        if (!$e->has()) {
            $d = $pagetype->createDraft($template);
            $d->setPageDraftTargetParentPageID($cParentID);
            $pagetype->savePageTypeComposerForm($d);
            if ($this->request->request('addPageComposeAction') == 'publish') {
                $pagetype->publish($d);
                $pr->setAdditionalDataAttribute('cParentID', $cParentID);
                $pr->setMessage(t('Page Added Successfully.'));
            } else {
                $pr->setRedirectURL($d->getCollectionLink(true));
            }
        }

        $pr->outputJSON();
    }
}
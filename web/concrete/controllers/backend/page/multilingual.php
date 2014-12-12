<?php
namespace Concrete\Controller\Backend\Page;
use Concrete\Core\Multilingual\Page\Section;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Controller\Backend\UserInterface\Page;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Core;

class Multilingual extends Page
{

    protected $controllerActionPath = '/ccm/system/page/multilingual';
    protected $newParent = false;
    protected $section;

    public function canAccess()
    {
        $ms = Section::getByID($this->request->request->get('section'));
        // we get the related parent id
        $cParentID = $this->page->getCollectionParentID();
        $cParent = \Page::getByID($cParentID);
        $cParentRelatedID = $ms->getTranslatedPageID($cParent);
        if ($cParentRelatedID > 0) {
            // we copy the page underneath it and store it
            $newParent = \Page::getByID($cParentRelatedID);
            $ct = \PageType::getByID($this->page->getPageTypeID());
            $cp = new \Permissions($newParent);
            if ($cp->canAddSubCollection($ct) && $this->page->canMoveCopyTo($newParent)) {
                $this->newParent = $newParent;
                $this->section = $ms;
                return true;
            }
        }
        return false;
    }
    public function create_new()
    {
        $pr = new PageEditResponse();
        $newPage = $this->page->duplicate($this->newParent);
        if (is_object($newPage)) {
            // grab the approved version and unapprove it
            $v = Version::get($newPage, 'ACTIVE');
            if (is_object($v)) {
                $v->deny();
                $pkr = new ApprovePageRequest();
                $pkr->setRequestedPage($newPage);
                $u = new \User();
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $response = $pkr->trigger();
                if (!($response instanceof Response)) {
                    // we are deferred
                    $pr->setMessage(t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.'));
                } else {
                    $ih = Core::make('multilingual/interface/flag');
                    $icon = $ih->getSectionFlagIcon($this->section);

                    $pr->setAdditionalDataAttribute('name', $newPage->getCollectionName());
                    $pr->setAdditionalDataAttribute('link', $newPage->getCollectionLink());
                    $pr->setAdditionalDataAttribute('icon', $icon);
                    $pr->setMessage(t('Page created.'));
                }
            }
        }
        $pr->outputJSON();
    }
}


<?php
namespace Concrete\Controller\Backend\Page;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Controller\Backend\UserInterface\Page;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Core;

class Multilingual extends Page
{

    protected $controllerActionPath = '/ccm/system/page/multilingual';

    public function canAccess()
    {
        return $this->permissions->canEditPageMultilingualSettings();
    }

    public function ignore()
    {
        $section = Section::getByID($_POST['section']);
        Section::ignorePageRelation($this->page, $section->getLocale());
        $r = new PageEditResponse();
        $r->setPage($this->page);
        $r->setMessage(t('Page ignored.'));
        $r->outputJSON();
    }

    public function unmap()
    {
        Section::unregisterPage($this->page);
        $r = new PageEditResponse();
        $r->setPage($this->page);
        $r->setMessage(t('Page unmapped.'));
        $r->outputJSON();
    }

    public function assign()
    {

        $pr = new PageEditResponse();

        if ($this->request->request->get('destID') == $this->page->getCollectionID()) {
            throw new \Exception(t("You cannot assign this page to itself."));
        }

        $destPage = \Page::getByID($_POST['destID']);
        if (Section::isMultilingualSection($destPage)) {
            $ms = Section::getByID($destPage->getCollectionID());
        } else {
            $ms = Section::getBySectionOfSite($destPage);
        }

        if (is_object($ms)) {
            // we need to assign/relate the source ID too, if it doesn't exist
            if (!Section::isAssigned($this->page)) {
                Section::registerPage($this->page);
            }
            Section::relatePage($this->page, $destPage, $ms->getLocale());
            $ih = Core::make('multilingual/interface/flag');
            $icon = $ih->getSectionFlagIcon($ms);
            $pr->setAdditionalDataAttribute('name', $destPage->getCollectionName());
            $pr->setAdditionalDataAttribute('link', $destPage->getCollectionLink());
            $pr->setAdditionalDataAttribute('icon', $icon);
            $pr->setMessage(t('Page assigned.'));
            $pr->outputJSON();
        } else {
            throw new \Exception(t("The destination page doesn't appear to be in a valid multilingual section."));
        }
    }

    public function create_new()
    {
        $pr = new PageEditResponse();
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
                $newPage = $this->page->duplicate($newParent);
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
                            $icon = $ih->getSectionFlagIcon($ms);
                            $pr->setAdditionalDataAttribute('name', $newPage->getCollectionName());
                            $pr->setAdditionalDataAttribute('link', $newPage->getCollectionLink());
                            $pr->setAdditionalDataAttribute('icon', $icon);
                            $pr->setMessage(t('Page created.'));
                        }
                    }
                }
            } else {
                throw new \Exception(t('You do not have permission to add this page to this section of the tree.'));
            }
        }
        $pr->outputJSON();
    }



}


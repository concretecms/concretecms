<?php
namespace Concrete\Core\Page;

use Concrete\Core\Page\Type\Event as PageTypeEvent;
use CollectionVersion;
use Concrete\Core\Page\Type\Type;
use User;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use Concrete\Core\Multilingual\Page\Section\Section;
use CacheLocal;
use Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;

class Publisher
{
    public function publish(Page $c, $requestOrDateTime = null, $cvPublishEndDate = null)
    {
        $this->stripEmptyPageTypeComposerControls($c);
        $parent = Page::getByID($c->getPageDraftTargetParentPageID());
        if ($c->isPageDraft()) { // this is still a draft, which means it has never been properly published.
            // so we need to move it, check its permissions, etc...
            Section::registerPage($c);
            $c->move($parent);
            $db = \Database::connection();
            $db->executeQuery('update Pages set cIsDraft = 0 where cID = ?', [$c->getCollectionID()]);
            if (!$parent->overrideTemplatePermissions()) {
                // that means the permissions of pages added beneath here inherit from page type permissions
                // this is a very poorly named method. Template actually used to mean Type.
                // so this means we need to set the permissions of this current page to inherit from page types.
                $c->inheritPermissionsFromDefaults();
            }
            $c->activate();
        } else {
            $c->rescanCollectionPath();
        }

        $u = new User();
        if (!($requestOrDateTime instanceof ApprovePagePageWorkflowRequest)) {
            $v = CollectionVersion::get($c, 'RECENT');
            $pkr = new ApprovePagePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $pkr->setRequestedVersionID($v->getVersionID());
            $pkr->setRequesterUserID($u->getUserID());
            if ($requestOrDateTime) {
                // That means it's a date time
                $pkr->scheduleVersion($requestOrDateTime, $cvPublishEndDate);
            }
        } else {
            $pkr = $requestOrDateTime;
        }
        $pkr->trigger();

        $u->unloadCollectionEdit($c);
        CacheLocal::flush();

        $ev = new PageTypeEvent($c);
        $type = $c->getPageTypeObject();
        if ($type instanceof Type) {
            $ev->setPageType($type);
        }
        $ev->setUser($u);

        \Events::dispatch('on_page_type_publish', $ev);
    }

    protected function stripEmptyPageTypeComposerControls(Page $c)
    {
        $type = $c->getPageTypeObject();
        if ($type instanceof Type) {
            $controls = PageTypeComposerControl::getList($type);
            foreach ($controls as $cn) {
                $cn->setPageObject($c);
                if ($cn->shouldPageTypeComposerControlStripEmptyValuesFromPage(
                    ) && $cn->isPageTypeComposerControlValueEmpty()
                ) {
                    $cn->removePageTypeComposerControlFromPage();
                }
            }
        }
    }
}

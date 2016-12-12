<?php
namespace Concrete\Controller\Panel\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Collection;
use Permissions;
use Page;
use Loader;
use Core;
use Config;
use CollectionVersion;
use Concrete\Core\Page\Collection\Version\EditResponse as PageEditVersionResponse;
use PageEditResponse;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use Concrete\Core\Page\Collection\Version\VersionList;
use User;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class Versions extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/versions';
    public function canAccess()
    {
        return $this->permissions->canViewPageVersions() || $this->permissions->canEditPageVersions();
    }

    protected function getPageVersionListResponse($currentPage = false)
    {
        $vl = new VersionList($this->page);
        $vl->setItemsPerPage(20);
        $vArray = $vl->getPage($currentPage);

        $r = new PageEditVersionResponse();
        $r->setPage($this->page);
        $r->setVersionList($vl);
        $cpCanDeletePageVersions = $this->permissions->canDeletePageVersions();
        foreach ($vArray as $v) {
            $r->addCollectionVersion($v);
        }

        return $r;
    }

    public function view()
    {
        $r = $this->getPageVersionListResponse();
        $this->set('response', $r);
    }

    public function get_json()
    {
        $currentPage = false;
        if ($_POST['currentPage']) {
            $currentPage = Loader::helper('security')->sanitizeInt($_POST['currentPage']);
        }
        $r = $this->getPageVersionListResponse($currentPage);
        $r->outputJSON();
    }

    public function duplicate()
    {
        if ($this->validateAction()) {
            $this->page->loadVersionObject($this->request->request->get('cvID'));
            $nc = $this->page->cloneVersion(t('Copy of Version: %s', $this->page->getVersionID()));
            $v = $nc->getVersionObject();
            $r = new PageEditVersionResponse();
            $r->setMessage(t('Version %s copied successfully. New version is %s.', $this->request->request->get('cvID'), $v->getVersionID()));
            $r->addCollectionVersion($v);
            $r->outputJSON();
        }
    }

    public function new_page()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $e = Core::make('helper/validation/error');
            $pt = $c->getPageTypeObject();
            if (is_object($pt)) {
                $ptp = new \Permissions($pt);
                if (!$ptp->canAddPageType()) {
                    $e->add(t('You do not have permission to create new pages of this type.'));
                }
            }

            $r = new PageEditVersionResponse();
            $r->setError($e);
            if (!$e->has()) {
                $c->loadVersionObject($_REQUEST['cvID']);
                $nc = $c->cloneVersion(t('New Page Created From Version'));
                $v = $nc->getVersionObject();
                $drafts = Page::getByPath(Config::get('concrete.paths.drafts'));
                $nc = $c->duplicate($drafts);
                $nc->deactivate();
                $nc->move($drafts);
                // now we delete all but the new version
                $vls = new VersionList($nc);
                $vls->setItemsPerPage(-1);
                $vArray = $vls->getPage();
                for ($i = 1; $i < count($vArray); ++$i) {
                    $cv = $vArray[$i];
                    $cv->delete();
                }
                // now, we delete the version we duped on the current page, since we don't need it anymore.
                $v->delete();
                // finally, we redirect the user to the new drafts page in composer mode.
                $r->setPage($nc);
                $r->setRedirectURL(\Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $nc->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
            }
            $r->outputJSON();
        }
    }

    public function delete()
    {
        if ($this->validateAction()) {
            $r = new PageEditVersionResponse();
            /** @var \Concrete\Core\Page\Collection\Collection $c */
            $c = $this->page;
            $versions = $this->countVersions($c);

            $cp = new Permissions($this->page);
            if ($cp->canDeletePageVersions()) {
                $r = new PageEditVersionResponse();
                $r->setPage($c);
                if (is_array($_POST['cvID'])) {
                    foreach ($_POST['cvID'] as $cvID) {
                        $v = CollectionVersion::get($c, $cvID);
                        if (is_object($v)) {
                            if ($versions == 1) {
                                $e = Loader::helper('validation/error');
                                $e->add(t('You cannot delete all page versions.'));
                                $r = new PageEditResponse($e);
                            } else if ($v->isApproved() && !$v->getPublishDate()) {
                                $e = Loader::helper('validation/error');
                                $e->add(t('You cannot delete the active version.'));
                                $r = new PageEditResponse($e);
                            } else {
                                $r->addCollectionVersion($v);
                                $v->delete();
                                $versions--;
                            }
                        }
                    }
                }
                if ($r instanceof PageEditVersionResponse) {
                    $r->setMessage(t2('%s version deleted successfully', '%s versions deleted successfully.',
                        count($r->getCollectionVersions())));
                }
            } else {
                $e = Loader::helper('validation/error');
                $e->add(t('You do not have permission to delete page versions.'));
                $r = new PageEditResponse($e);
            }
            $r->outputJSON();
        }
    }


    private function countVersions(Collection $c)
    {
        /** @var Connection $database */
        $database = $this->app['database']->connection();
        $count = $database->fetchColumn('select count(cvID) from CollectionVersions where cID = :cID', [
            ':cID' => $c->getCollectionID()
        ]);

        return $count;
    }

    public function approve()
    {
        $c = $this->page;
        $cp = $this->permissions;
        if ($this->validateAction()) {
            $r = new PageEditVersionResponse();
            if ($cp->canApprovePageVersions()) {
                $ov = CollectionVersion::get($c, 'ACTIVE');
                if (is_object($ov)) {
                    $ovID = $ov->getVersionID();
                }
                $nvID = $_REQUEST['cvID'];

                $r = new PageEditVersionResponse();
                $r->setPage($c);
                $u = new User();
                $pkr = new ApprovePagePageWorkflowRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, $_REQUEST['cvID']);
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $response = $pkr->trigger();
                if (!($response instanceof WorkflowProgressResponse)) {
                    // we are deferred
                    $r->setMessage(t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.'));
                } else {
                    if ($ovID) {
                        $r->addCollectionVersion(CollectionVersion::get($c, $ovID));
                    }
                    $r->addCollectionVersion(CollectionVersion::get($c, $nvID));
                    $r->setMessage(t('Version %s approved successfully', $v->getVersionID()));
                }
            } else {
                $e = Loader::helper('validation/error');
                $e->add(t('You do not have permission to approve page versions.'));
                $r = new PageEditResponse($e);
            }

            $r->outputJSON();
        }
    }
}

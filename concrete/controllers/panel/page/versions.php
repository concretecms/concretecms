<?php

namespace Concrete\Controller\Panel\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\EditResponse as PageEditVersionResponse;
use Concrete\Core\Page\Collection\Version\Version as CollectionVersion;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use Concrete\Core\Workflow\Request\UnapprovePageRequest;

class Versions extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/versions';

    public function canAccess()
    {
        return $this->permissions->canViewPageVersions() || $this->permissions->canEditPageVersions();
    }

    public function view()
    {
        $r = $this->getPageVersionListResponse();
        $this->set('response', $r);
    }

    public function get_json()
    {
        $currentPage = false;
        if ($this->request->request->has('currentPage')) {
            $currentPage = $this->app->make('helper/security')->sanitizeInt($this->request->request->get('currentPage'));
        }
        $r = $this->getPageVersionListResponse($currentPage);

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
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

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
        }
    }

    public function new_page()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $e = $this->app->make('helper/validation/error');
            $pt = $c->getPageTypeObject();
            if (is_object($pt)) {
                $ptp = new Permissions($pt);
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
                $drafts = Page::getDraftsParentPage();
                $nc = $c->duplicate($drafts);
                $nc->deactivate();
                $nc->setPageToDraft();
                $nc->move($drafts);
                // now we delete all but the new version
                $vls = new VersionList($nc);
                $vls->setItemsPerPage(-1);
                $vArray = $vls->getPage();
                for ($i = 1; $i < count($vArray); $i++) {
                    $cv = $vArray[$i];
                    $cv->delete();
                }
                // now, we delete the version we duped on the current page, since we don't need it anymore.
                $v->delete();
                // finally, we redirect the user to the new drafts page in composer mode.
                $r->setPage($nc);
                $r->setRedirectURL(
                    $this->app->make(ResolverManagerInterface::class)->resolve([
                        "/ccm/system/page/checkout/{$nc->getCollectionID()}/first/" . $this->app->make('token')->generate(),
                    ])
                );
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
        }
    }

    public function delete()
    {
        if ($this->validateAction()) {
            $r = new PageEditVersionResponse();
            $c = $this->page;
            $versions = $this->countVersions($c);

            $cp = new Permissions($this->page);
            if ($cp->canDeletePageVersions()) {
                $r = new PageEditVersionResponse();
                $r->setPage($c);
                if (is_array($this->request->request->get('cvID'))) {
                    foreach ($this->request->request->get('cvID') as $cvID) {
                        $v = CollectionVersion::get($c, $cvID);
                        if (is_object($v)) {
                            if ($versions == 1) {
                                $e = $this->app->make('helper/validation/error');
                                $e->add(t('You cannot delete all page versions.'));
                                $r = new PageEditResponse($e);
                            } elseif ($v->isApprovedNow()) {
                                $e = $this->app->make('helper/validation/error');
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
                    $r->setMessage(t2(
                        '%s version deleted successfully',
                        '%s versions deleted successfully.',
                        count($r->getCollectionVersions())
                    ));
                }
            } else {
                $e = $this->app->make('helper/validation/error');
                $e->add(t('You do not have permission to delete page versions.'));
                $r = new PageEditResponse($e);
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
        }
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
                $nvID = $this->request->request->get('cvID');

                $r = new PageEditVersionResponse();
                $r->setPage($c);
                $u = $this->app->make(User::class);
                $pkr = new ApprovePagePageWorkflowRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, $_REQUEST['cvID']);
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                // We keep other scheduling if you click approve from versions panel
                $pkr->setKeepOtherScheduling(true);
                $response = $pkr->trigger();
                if (!($response instanceof WorkflowProgressResponse)) {
                    // we are deferred
                    $r->setMessage(t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.'));
                } else {
                    if (isset($ovID) && $ovID) {
                        $r->addCollectionVersion(CollectionVersion::get($c, $ovID));
                    }
                    $r->addCollectionVersion(CollectionVersion::get($c, $nvID));
                    $r->setMessage(t('Version %s approved successfully', $v->getVersionID()));
                }
            } else {
                $e = $this->app->make('helper/validation/error');
                $e->add(t('You do not have permission to approve page versions.'));
                $r = new PageEditResponse($e);
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
        }
    }

    public function unapprove()
    {
        $c = $this->page;
        $cp = $this->permissions;
        if ($this->validateAction()) {
            $r = new PageEditVersionResponse();
            if ($cp->canApprovePageVersions()) {
                $cvID = $this->request->request->get('cvID');
                $r = new PageEditVersionResponse();
                $r->setPage($c);
                $u = $this->app->make(User::class);
                $pkr = new UnapprovePageRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, $_REQUEST['cvID']);
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $response = $pkr->trigger();
                if (!($response instanceof WorkflowProgressResponse)) {
                    // we are deferred
                    $r->setMessage(t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.'));
                } else {
                    $r->addCollectionVersion(CollectionVersion::get($c, $cvID));
                    $r->setMessage(t('Version %s unapproved successfully', $v->getVersionID()));
                }
            } else {
                $e = $this->app->make('helper/validation/error');
                $e->add(t('You do not have permission to approve page versions.'));
                $r = new PageEditResponse($e);
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSON());
        }
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

    private function countVersions(Collection $c)
    {
        /** @var Connection $database */
        $database = $this->app['database']->connection();

        return $database->fetchOne('select count(cvID) from CollectionVersions where cID = :cID', [
            ':cID' => $c->getCollectionID(),
        ]);
    }
}

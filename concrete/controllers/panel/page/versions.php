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
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use Concrete\Core\Workflow\Request\UnapprovePageRequest;
use Doctrine\DBAL\Types\Types;

class Versions extends BackendInterfacePageController
{
    /**
     * @var string
     */
    protected $viewPath = '/panels/page/versions';

    /**
     * @return bool
     */
    public function canAccess()
    {
        return $this->permissions->canViewPageVersions() || $this->permissions->canEditPageVersions();
    }

    /**
     * @return void
     */
    public function view()
    {
        $r = $this->getPageVersionListResponse();
        $this->set('response', $r);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function get_json()
    {
        $currentPage = 0;
        if ($this->request->request->has('currentPage')) {
            $currentPage = (int) $this->app->make('helper/security')->sanitizeInt($this->request->request->get('currentPage'));
        }
        $r = $this->getPageVersionListResponse($currentPage);

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function duplicate()
    {
        if ($this->validateAction()) {
            $versionID = app('helper/security')->sanitizeInt($this->request->request->get('cvID'));
            $this->page->loadVersionObject($versionID);
            $r = new PageEditVersionResponse();
            $r->setPage($this->page);
            if ($this->page->getVersionID()) {
                $nc = $this->page->cloneVersion(t('Copy of Version: %s', $this->page->getVersionID()));
                $v = $nc->getVersionObject();

                $r->setMessage(t('Version %s copied successfully. New version is %s.', $versionID, $v->getVersionID()));
                $r->addCollectionVersion($v);
            } else {
                $this->error->add(t('Invalid Version ID'));
                $r->setError($this->error);
            }

        } else {
            $this->error->add(t('You can not perform this action.'));
            $r = new PageEditVersionResponse($this->error);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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
                $cvID = app('helper/security')->sanitizeInt($this->request->request->get('cvID'));
                $c->loadVersionObject($cvID);
                if ($c->getVersionID()) {
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
                    for ($i = 1, $iMax = count($vArray); $i < $iMax; $i++) {
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
                } else {
                    $this->error->add(t('Invalid Version ID'));
                    $r->setError($this->error);
                }
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
        }

        $this->error->add(t('You do not have permission to create new pages of this type.'));
        $r = new PageEditVersionResponse($this->error);

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete()
    {
        if ($this->validateAction()) {
            $r = new PageEditVersionResponse();
            $c = $this->page;
            $versions = $this->countVersions($c);

            $cp = new Permissions($this->page);
            if ($cp->canDeletePageVersions()) {
                $r->setPage($c);
                $cvIDs = $this->request->request->get('cvID');
                if (is_array($cvIDs)) {
                    foreach ($cvIDs as $cvID) {
                        $v = CollectionVersion::get($c, $cvID);
                        if (is_object($v)) {
                            if ($versions === 1) {
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

                return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
            }
        }
        $e = $this->app->make('helper/validation/error');
        $e->add(t('You do not have permission to delete page versions.'));
        $r = new PageEditResponse($e);

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function approve()
    {
        $c = $this->page;
        $cp = $this->permissions;
        if ($this->validateAction() && $cp->canApprovePageVersions()) {
            $ov = CollectionVersion::get($c, 'ACTIVE');
            $ovID = null;
            if (is_object($ov)) {
                $ovID = $ov->getVersionID();
            }
            $nvID = app('helper/security')->sanitizeInt($this->request->request->get('cvID'));

            $r = new PageEditVersionResponse();
            $r->setPage($c);
            $u = $this->app->make(User::class);
            $pkr = new ApprovePagePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $v = CollectionVersion::get($c, $nvID);
            $pkr->setRequestedVersionID($v->getVersionID());
            $pkr->setRequesterUserID($u->getUserID());
            // We keep other scheduling if you click approve from versions panel
            $pkr->setKeepOtherScheduling(true);
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
            $e = $this->app->make('helper/validation/error');
            $e->add(t('You do not have permission to approve page versions.'));
            $r = new PageEditResponse($e);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    public function revert()
    {
        /** @var Page $page */
        $page = $this->page;
        $this->validationToken = 'revert_page';
        if ($this->validateAction()) {
            /** @var Checker $cp */
            $cp = $this->permissions;
            if (!$cp->canDeletePage()) {
                $this->error->add(t('You do not have permission to delete this page.'));
            }
            $type = $page->getPageTypeObject();
            $tp = new Checker($type);
            if (!$tp->canAddPageType()) {
                $this->error->add(t('You do not have permission to add a page of this type.'));
            }
        } else {
            $this->error->add(t('Access Denied.'));
        }

        /** @var ResponseFactoryInterface $factory */
        $factory = $this->app->make(ResponseFactoryInterface::class);
        $response = new PageEditResponse($this->error);
        $response->setError($this->error);

        if (!$this->error->has()) {
            // create a new page from the current page
            $page->loadVersionObject('RECENT');
            $page = $page->cloneVersion(t('New Reverted Page'));
            $drafts = Page::getDraftsParentPage();
            $newPage = $page->duplicate($drafts);
            $newPage->deactivate();
            $newPage->setPageToDraft();
            $newPage->move($drafts);

            // now we delete all but the new version
            $versionList = new VersionList($newPage);
            $versionList->setItems(-1);
            $versions = $versionList->getPage();
            for ($i = 1; $i < count($versions); ++$i) {
                $cv = $versions[$i];
                $cv->delete();
            }

            // now we delete the current page
            $page->moveToTrash();

            // finally, we redirect the user to the new draft page in composer mode.
            $response->setPage($newPage);
            $response->setRedirectURL(
                $this->app->make(ResolverManagerInterface::class)->resolve([
                    '/ccm/system/page/checkout', $newPage->getCollectionID(), 'first', $this->app->make('token')->generate()
                ])
            );
        }

        return $factory->json($response->getJSONObject());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function unapprove()
    {
        $c = $this->page;
        $cp = $this->permissions;
        if ($this->validateAction() && $cp->canApprovePageVersions()) {
            $cvID = app('helper/security')->sanitizeInt($this->request->request->get('cvID'));
            $r = new PageEditVersionResponse();
            $r->setPage($c);
            $u = $this->app->make(User::class);
            $pkr = new UnapprovePageRequest();
            $pkr->setRequestedPage($c);
            $v = CollectionVersion::get($c, $cvID);
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

        return $this->app->make(ResponseFactoryInterface::class)->json($r->getJSONObject());
    }

    /**
     * @param int $currentPage The current page of the list (int)
     *
     * @return PageEditVersionResponse
     */
    protected function getPageVersionListResponse(int $currentPage = 0): PageEditVersionResponse
    {
        $vl = new VersionList($this->page);
        $vl->setItemsPerPage(20);
        $vArray = $vl->getPage($currentPage);

        $r = new PageEditVersionResponse();
        $r->setPage($this->page);
        $r->setVersionList($vl);
        foreach ($vArray as $v) {
            $r->addCollectionVersion($v);
        }

        return $r;
    }

    /**
     * @param Collection $c
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return int
     */
    private function countVersions(Collection $c): int
    {
        /** @var Connection $database */
        $database = $this->app['database']->connection();

        return (int) $database->fetchOne('select count(cvID) from CollectionVersions where cID = :cID', [
            ':cID' => $c->getCollectionID(),
        ], [Types::INTEGER]);
    }
}

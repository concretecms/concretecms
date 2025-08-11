<?php
namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Controller\Traits\DashboardBulkUpdaterTrait;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class Design extends BackendInterfaceController
{
    use DashboardBulkUpdaterTrait;
    protected $viewPath = '/dialogs/page/bulk/design';

    protected function getObjectFromRequestId(string $id)
    {
        $page = Page::getByID($id, 'RECENT');
        if ($page && !$page->isError()) {
            return $page;
        }
        return null;
    }

    public function canPerformOperationOnObject($object): bool
    {
        $checker = new Checker($object);
        return $checker->canEditPageTemplate() && $checker->canEditPageType() && $checker->canEditPageTheme();
    }

    public function view()
    {
        $this->populateItemsFromRequest();
        $selectedThemeID = null;
        $selectedTemplateID = null;
        $selectedTypeID = null;
        $containsSinglePages = false;
        foreach ($this->items as $page) {
            /**
             * @var Page $page
             */
            $pThemeID = $page->getCollectionThemeID();
            if ($pThemeID) {
                $selectedTheme = Theme::getByID($pThemeID);
            } else {
                $selectedTheme = Theme::getSiteTheme();
            }
            if ($selectedThemeID === null) {
                $selectedThemeID = $selectedTheme->getThemeID();
            } else if ($selectedThemeID !== $selectedTheme->getThemeID()) {
                $selectedThemeID = -1; // multiple
            }

            if ($selectedTemplateID === null) {
                $selectedTemplateID = $page->getPageTemplateID();
            } else if ($selectedTemplateID !== $page->getPageTemplateID()) {
                $selectedTemplateID = -1; // multiple
            }

            if ($selectedTypeID === null) {
                $selectedTypeID = $page->getPageTypeID();
            } else if ($selectedTypeID !== $page->getPageTypeID()) {
                $selectedTypeID = -1; // multiple
            }

            if ($page->isGeneratedCollection()) {
                $containsSinglePages = true;
            }
        }

        $themesSelect = [];
        $templatesSelect = [];
        $typesSelect = [];
        if ($selectedThemeID === -1) {
            $themesSelect[-1] = t('** Multiple Values');
        }
        if ($selectedTemplateID === -1) {
            $templatesSelect[-1] = t('** Multiple Values');
        }
        if ($selectedTypeID === -1) {
            $typesSelect[-1] = t('** Multiple Values');
        }
        foreach (Theme::getList() as $pt) {
            $themesSelect[$pt->getThemeID()] = $pt->getThemeDisplayName();
        }
        foreach (Template::getList() as $template) {
            $templatesSelect[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
        }
        foreach (Type::getList() as $type) {
            $typesSelect[$type->getPageTypeID()] = $type->getPageTypeDisplayName();
        }

        $this->set('form', $this->app->make('helper/form'));
        $this->set('dh', $this->app->make('helper/date'));
        $this->set('pages', $this->items);
        $this->set('selectedThemeID', $selectedThemeID);
        $this->set('selectedTemplateID', $selectedTemplateID);
        $this->set('selectedTypeID', $selectedTypeID);
        $this->set('themesSelect', $themesSelect);
        $this->set('templatesSelect', $templatesSelect);
        $this->set('typesSelect', $typesSelect);
        $this->set('containsSinglePages', $containsSinglePages);
    }

    public function submit()
    {
        if ($this->canAccess()) {
            $containsSinglePages = false;
            foreach ($this->items as $page) {
                if ($page->isGeneratedCollection()) {
                    $containsSinglePages = true;
                }
            }


            $theme = null;
            $template = null;
            $type = null;
            if (!$containsSinglePages) {
                if ($this->request->request->getInt('pTemplateID') > 0) {
                    $template = Template::getByID($this->request->request->getInt('pTemplateID'));
                }
            }

            if ($this->request->request->getInt('pTypeID') > 0) {
                $type = Type::getByID($this->request->request->getInt('pTypeID'));
            }

            if ($this->request->request->getInt('pThemeID') > 0) {
                $theme = Theme::getByID($this->request->request->getInt('pThemeID'));
            }

            if ($template || $theme || $type) {
                foreach ($this->items as $page) {
                    $nvc = $page->getVersionToModify();
                    if ($template) {
                        $nvc->update(['pTemplateID' => $template->getPageTemplateID()]);
                    }
                    if ($theme) {
                        $nvc->setTheme($theme);
                    }
                    if ($type) {
                        $nvc->setPageType($type);
                    }

                    $checker = new Checker($page);
                    $config = $this->app->make('config');
                    if ($checker->canApprovePageVersions() && $config->get('concrete.misc.sitemap_approve_immediately')) {
                        $pkr = new ApprovePageRequest();
                        $u = $this->app->make(User::class);
                        $pkr->setRequestedPage($page);
                        $v = Version::get($page, "RECENT");
                        $pkr->setRequestedVersionID($v->getVersionID());
                        $pkr->setRequesterUserID($u->getUserID());
                        $pkr->trigger();
                    }
                }
            }

            $response = new EditResponse();
            $response->setMessage(t('Page design settings updated successfully.'));
            return new JsonResponse($response);
        } else {
            throw new \RuntimeException(t('Access Denied'));
        }
    }


}

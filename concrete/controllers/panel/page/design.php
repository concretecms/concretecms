<?php
namespace Concrete\Controller\Panel\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendUIPageController;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Page\View\Preview\PageDesignPreviewRequest;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\View\View;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Request\ApprovePageRequest;

class Design extends BackendUIPageController
{
    protected $viewPath = '/panels/page/design';
    public function canAccess()
    {
        return $this->permissions->canEditPageTemplate() || $this->permissions->canEditPageTheme();
    }

    public function view()
    {
        $c = $this->page;
        $cp = $this->permissions;

        $pagetype = $c->getPageTypeObject();
        if (is_object($pagetype)) {
            $_templates = $pagetype->getPageTypePageTemplateObjects();
        } else {
            $_templates = Template::getList();
        }

        $pTemplateID = $c->getPageTemplateID();
        $templates = array();
        if ($pTemplateID) {
            $selectedTemplate = Template::getByID($pTemplateID);
            $templates[] = $selectedTemplate;
        }

        foreach ($_templates as $tmp) {
            if (!in_array($tmp, $templates)) {
                $templates[] = $tmp;
            }
        }

        $tArrayTmp = array_merge(Theme::getGlobalList(), Theme::getLocalList());
        $_themes = array();
        foreach ($tArrayTmp as $pt) {
            if ($cp->canEditPageTheme($pt)) {
                $_themes[] = $pt;
            }
        }

        $pThemeID = $c->getCollectionThemeID();
        if ($pThemeID) {
            $selectedTheme = Theme::getByID($pThemeID);
        } else {
            $selectedTheme = Theme::getSiteTheme();
        }

        $themes = array($selectedTheme);
        foreach ($_themes as $t) {
            if (!in_array($t, $themes)) {
                $themes[] = $t;
            }
        }

        $templatesSelect = array();
        $themesSelect = array();
        foreach ($_themes as $pt) {
            $themesSelect[$pt->getThemeID()] = $pt->getThemeDisplayName();
        }
        foreach ($_templates as $pt) {
            $templatesSelect[$pt->getPageTemplateID()] = $pt->getPageTemplateDisplayName();
        }

        $typesSelect = array('0' => t('** None'));
        $tree = $c->getSiteTreeObject();
        if (is_object($tree)) {
            $type = $tree->getSiteType();
            $typeList = Type::getList(false, $type);
            foreach ($typeList as $_pagetype) {
                $typesSelect[$_pagetype->getPageTypeID()] = $_pagetype->getPageTypeDisplayName();
            }
        }

        $this->set('availableSummaryTemplatesCount', count($this->page->getSummaryTemplates()));
        $this->set('templatesSelect', $templatesSelect);
        $this->set('themesSelect', $themesSelect);
        $this->set('themes', $themes);
        $this->set('templates', $templates);
        $this->set('typesSelect', $typesSelect);
        $this->set('selectedTheme', $selectedTheme);
        $this->set('selectedType', $pagetype);
        $this->set('selectedTemplate', $selectedTemplate);
    }

    public function preview()
    {
        $this->setViewObject(new View('/panels/details/page/preview'));
    }

    public function preview_contents()
    {
        $req = Request::getInstance();
        $req->setCurrentPage($this->page);
        $controller = $this->page->getPageController();
        $view = $controller->getViewObject();

        $previewRequest = new PageDesignPreviewRequest();
        if ($this->request->query->has('pTemplateID')) {
            $pt = Template::getByID(h($this->request->query->get('pTemplateID')));
            if ($pt) {
                $previewRequest->setPageTemplate($pt);
            }
        }
        if ($this->request->query->has('pThemeID')) {
            $pt = Theme::getByID(h($this->request->query->get('pThemeID')));
        } else {
            $pt = $this->page->getCollectionThemeObject();
        }
        if ($pt) {
            $previewRequest->setTheme($pt);
            if ($this->request->query->has('skinIdentifier')) {
                $skin = $pt->getSkinByIdentifier(h($this->request->query->get('skinIdentifier')));
            } else {
                $skin = $this->page->getPageSkin();
            }
            if ($skin) {
                $previewRequest->setSkin($skin);
            }
        }

        $view->setCustomPreviewRequest($previewRequest);
        $req->setCustomRequestUser(-1);
        $response = new \Symfony\Component\HttpFoundation\Response();
        $content = $view->render();
        $response->setContent($content);
        return $response;
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $cp = $this->permissions;
            $c = $this->page;

            $nvc = $c->getVersionToModify();

            if ($this->permissions->canEditPageTheme()) {
                $pl = false;
                if ($this->request->request->has('pThemeID')) {
                    $pl = Theme::getByID($_POST['pThemeID']);
                }
                $data = array();
                if (is_object($pl)) {
                    $nvc->setTheme($pl);
                    if ($this->request->request->has('skinIdentifier')) {
                        if ($pl->hasSkins() && ($skin = $pl->getSkinByIdentifier(h($this->request->request->get('skinIdentifier'))))) {
                            $nvc->setThemeSkin($skin);
                        }
                    }
                }
            }

            if (!$c->isGeneratedCollection()) {
                if ($_POST['pTemplateID'] && $cp->canEditPageTemplate()) {
                    // now we have to check to see if you're allowed to update this page to this page type.
                    // We do this by checking to see whether the PARENT page allows you to add this page type here.
                    // if this is the home page then we assume you are good

                    $template = Template::getByID($_POST['pTemplateID']);
                    $proceed = true;
                    $pagetype = $c->getPageTypeObject();
                    if (is_object($pagetype)) {
                        $templates = $pagetype->getPageTypePageTemplateObjects();
                        if (!in_array($template, $templates)) {
                            $proceed = false;
                        }
                    }
                    if ($proceed) {
                        $data['pTemplateID'] = $_POST['pTemplateID'];
                        $nvc->update($data);
                    }
                }

                if ($cp->canEditPageType()) {
                    $ptID = $c->getPageTypeID();
                    if ($ptID != $_POST['ptID']) {
                        // the page type has changed.
                        if ($_POST['ptID']) {
                            $type = Type::getByID($_POST['ptID']);
                            if (is_object($type)) {
                                $nvc->setPageType($type);
                            }
                        } else {
                            $nvc->setPageType(null);
                        }
                    }
                }
            }

            $r = new PageEditResponse();
            $r->setPage($c);
            if ($this->request->request->get('sitemap')) {
                $r->setMessage(t('Page updated successfully.'));
                $config = $this->app->make('config');
                if ($this->permissions->canApprovePageVersions() && $config->get('concrete.misc.sitemap_approve_immediately')) {
                    $pkr = new ApprovePageRequest();
                    $u = $this->app->make(User::class);
                    $pkr->setRequestedPage($this->page);
                    $v = Version::get($this->page, "RECENT");
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    $u->unloadCollectionEdit();
                }
            } else {
                $r->setRedirectURL(\URL::to($c));
            }
            $r->outputJSON();
        }
    }
}

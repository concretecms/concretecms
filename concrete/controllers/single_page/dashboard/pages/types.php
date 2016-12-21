<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Controller\Element\Dashboard\Pages\Types\Header;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use PageType;
use Loader;
use PageTemplate;

class Types extends DashboardPageController
{
    public function page_type_added($siteTypeID = null)
    {
        $this->set('success', t('Page Type added successfully.'));
        $this->view($siteTypeID);
    }

    public function page_type_updated($siteTypeID = null)
    {
        $this->set('success', t('Page type updated successfully.'));
        $this->view($siteTypeID);
    }

    public function page_type_duplicated($siteTypeID = null)
    {
        $this->set('success',
            t('Page type copied successfully. Important! You will need to re-add any blocks into output areas that you had set on the original page type.'));
        $this->view($siteTypeID);
    }

    public function page_type_deleted($siteTypeID = null)
    {
        $this->set('success', t('Page type deleted successfully.'));
        $this->view($siteTypeID);
    }

    public function edit($ptID = false)
    {
        $cm = PageType::getByID($ptID);
        $cmp = new \Permissions($cm);
        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }
        $this->set('pagetype', $cm);
        $this->set('pageTitle', t('Edit %s Page Type', $cm->getPageTypeDisplayName()));
    }

    public function view($typeID = 0)
    {
        $siteType = false;
        if ($typeID) {
            $siteType = $this->app->make('site/type')->getByID($typeID);
        }
        if (!$siteType) {
            $siteType = $this->app->make('site/type')->getDefault();
        } else {
            $this->set('siteTypeID', $siteType->getSiteTypeID());
        }
        $pagetypes = PageType::getList(false, $siteType);
        $this->set('headerMenu', new Header($siteType));
        $this->set('pagetypes', $pagetypes);

        $siteTypes = array();
        foreach(\Core::make('site/type')->getList() as $siteType) {
            $siteTypes[$siteType->getSiteTypeID()] = $siteType->getSiteTypeName();
        }
        $this->set('siteTypes', $siteTypes);
    }

    public function delete($ptID = false)
    {
        $pagetype = PageType::getByID($ptID);
        if (!is_object($pagetype)) {
            $this->error->add(t('Invalid page type object.'));
        }
        $cmp = new \Permissions($pagetype);
        if (!$cmp->canDeletePageType()) {
            $this->error->add(t('You do not have access to delete this page type.'));
        }

        $count = $pagetype->getPageTypeUsageCount();
        if ($count > 0) {
            $this->error->add(t2(
                'This page type is in use on %d page.',
                'This page type is in use on %d pages.', $count));
        }

        if (!$this->token->validate('delete_page_type')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }
        if (!$this->error->has()) {
            $pagetype->delete();
            $this->redirect('/dashboard/pages/types', 'page_type_deleted', $pagetype->getSiteTypeID());
        }
        $this->view();
    }

    public function duplicate($ptID = false)
    {
        $pagetype = PageType::getByID($ptID);
        if (!is_object($pagetype)) {
            $this->error->add(t('Invalid page type object.'));
        }
        if (!$this->token->validate('duplicate_page_type')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }
        $vs = Loader::helper('validation/strings');
        $sec = Loader::helper('security');
        $name = $sec->sanitizeString($this->post('ptName'));
        $handle = $sec->sanitizeString($this->post('ptHandle'));
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your page type.'));
        }
        if (!$vs->handle($handle)) {
            $this->error->add(t('You must specify a valid handle for your page type.'));
        }
        $siteType = null;
        if ($this->request->request->has('siteType')) {
            $siteType = $this->app->make('site/type')->getByID($this->request->request->get('siteType'));
        }
        if (!$this->error->has()) {
            $pagetype->duplicate($handle, $name, $siteType);
            $this->redirect('/dashboard/pages/types', 'page_type_duplicated', $pagetype->getSiteTypeID());
        }
        $this->view();
    }

    public function submit($ptID = false)
    {
        $pagetype = PageType::getByID($ptID);
        if (!is_object($pagetype)) {
            $this->error->add(t('Invalid page type object.'));
        }
        if (!$this->token->validate('update_page_type')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        $vs = Loader::helper('validation/strings');
        $sec = Loader::helper('security');
        $name = $sec->sanitizeString($this->post('ptName'));
        $handle = $sec->sanitizeString($this->post('ptHandle'));
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your page type.'));
        }
        if (!$vs->handle($handle)) {
            $this->error->add(t('You must specify a valid handle for your page type.'));
        } else {
            $type2 = PageType::getByHandle($handle);
            if (is_object($type2) && $type2->getPageTypeID() != $pagetype->getPageTypeID()) {
                $this->error->add(t('You must specify a unique handle for your page type.'));
            }
        }
        $defaultTemplate = PageTemplate::getByID($this->post('ptDefaultPageTemplateID'));
        if (!is_object($defaultTemplate)) {
            $this->error->add(t('You must choose a valid default page template.'));
        }
        $templates = array();
        if (is_array($_POST['ptPageTemplateID'])) {
            foreach ($this->post('ptPageTemplateID') as $pageTemplateID) {
                $pt = PageTemplate::getByID($pageTemplateID);
                if (is_object($pt)) {
                    $templates[] = $pt;
                }
            }
        }

        if (count($templates) == 0 && $this->post('ptAllowedPageTemplates') == 'C') {
            $this->error->add(t('You must specify at least one page template.'));
        }
        $target = \Concrete\Core\Page\Type\PublishTarget\Type\Type::getByID($this->post('ptPublishTargetTypeID'));
        if (!is_object($target)) {
            $this->error->add(t('Invalid page type target type.'));
        } else {
            $pe = $target->validatePageTypeRequest($this->request);
            if ($pe instanceof ErrorList) {
                $this->error->add($pe);
            }
        }
        if (!$this->error->has()) {
            $data = array(
                'handle' => $handle,
                'name' => $name,
                'defaultTemplate' => $defaultTemplate,
                'ptLaunchInComposer' => $this->post('ptLaunchInComposer'),
                'ptIsFrequentlyAdded' => $this->post('ptIsFrequentlyAdded'),
                'allowedTemplates' => $this->post('ptAllowedPageTemplates'),
                'templates' => $templates,
            );
            $pagetype->update($data);
            $configuredTarget = $target->configurePageTypePublishTarget($pagetype, $this->post());
            $pagetype->setConfiguredPageTypePublishTargetObject($configuredTarget);
            $this->redirect('/dashboard/pages/types', 'page_type_updated', $pagetype->getSiteTypeID());
        }
        $this->edit($ptID);
    }
}

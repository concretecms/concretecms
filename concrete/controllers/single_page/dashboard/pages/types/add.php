<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Controller\DashboardPageController;
use PageType;
use PageTemplate;
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;

class Add extends DashboardPageController
{

    public function view($typeID = 0)
    {
        $siteType = false;
        if ($typeID) {
            $siteType = $this->app->make('site/type')->getByID($typeID);
        }
        if (!$siteType) {
            $siteType = $this->app->make('site/type')->getDefault();
        }

        $this->set('siteType', $siteType);
    }

    public function submit()
    {
        $post = $this->request->request;
        $vs = $this->app->make('helper/validation/strings');
        $sec = $this->app->make('helper/security');
        $name = $sec->sanitizeString($post->get('ptName'));
        $handle = $sec->sanitizeString($post->get('ptHandle'));
        if (!$this->token->validate('add_page_type')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }
        if (!$vs->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your page type.'));
        }
        if (!$vs->handle($handle)) {
            $this->error->add(t('You must specify a valid handle for your page type.'));
        } else {
            $_pt = PageType::getByHandle($handle);
            if (is_object($_pt)) {
                $this->error->add(t('You must specify a unique handle for your page type.'));
            }
            unset($_pt);
        }
        $defaultTemplate = PageTemplate::getByID($post->get('ptDefaultPageTemplateID'));
        if (!is_object($defaultTemplate)) {
            $this->error->add(t('You must choose a valid default page template.'));
        }
        $templates = array();
        if (is_array($post->get('ptPageTemplateID'))) {
            foreach ($post->get('ptPageTemplateID') as $pageTemplateID) {
                $pt = PageTemplate::getByID($pageTemplateID);
                if (is_object($pt)) {
                    $templates[] = $pt;
                }
            }
        }
        if (count($templates) == 0 && $post->get('ptAllowedPageTemplates') == 'C') {
            $this->error->add(t('You must specify at least one page template.'));
        }
        $target = PageTypePublishTargetType::getByID($post->get('ptPublishTargetTypeID'));
        if (!is_object($target)) {
            $this->error->add(t('Invalid page type publish target type.'));
        } else {
            $pe = $target->validatePageTypeRequest($this->request);
            if ($pe instanceof ErrorList) {
                $this->error->add($pe);
            }
        }

        $siteTypeID = null;
        if (!$this->error->has()) {
            $siteType = $this->app->make('site/type')->getByID($post->get('siteTypeID'));
            if (!is_object($siteType)) {
                $siteType = $this->app->make('site/type')->getDefault();
            } else {
                $siteTypeID = $siteType->getSiteTypeID();
            }

            $data = array(
                'handle' => $handle,
                'name' => $name,
                'defaultTemplate' => $defaultTemplate,
                'ptLaunchInComposer' => $post->get('ptLaunchInComposer'),
                'ptIsFrequentlyAdded' => $post->get('ptIsFrequentlyAdded'),
                'allowedTemplates' => $post->get('ptAllowedPageTemplates'),
                'templates' => $templates,
                'siteType' => $siteType
            );
            $pt = PageType::add($data);
            $configuredTarget = $target->configurePageTypePublishTarget($pt, $post->all());
            $pt->setConfiguredPageTypePublishTargetObject($configuredTarget);
            $this->redirect('/dashboard/pages/types', 'page_type_added', $siteTypeID);
        }
        $this->view($siteTypeID);
    }
}

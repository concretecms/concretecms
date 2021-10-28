<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Page\Controller\DashboardPageController;
use Exception;
use PageTemplate;
use PageType;

class Templates extends DashboardPageController
{
    public function view()
    {
        $this->set('templates', PageTemplate::getList());
    }

    public function delete($pTemplateID, $token = '')
    {
        $valt = $this->app->make('helper/validation/token');
        if (!$valt->validate('delete_page_template', $token)) {
            $this->set('message', $valt->getErrorMessage());
        } else {
            $pageTypes = PageType::getListByDefaultPageTemplate($pTemplateID);
            if (count($pageTypes)) {
                $handles = [];
                foreach ($pageTypes as $pt) {
                    $handles[] = $pt->getPageTypeHandle();
                }
                $this->set('message', t(
                    'You cannot delete a template that is set as the default for any of the page types. ' .
                    'Please change the default template for the following page types first: %s.',
                    implode(', ', $handles)
                ));
            } else {
                $pt = PageTemplate::getByID($pTemplateID);
                if (is_object($pt)) {
                    $pt->delete();

                    return $this->buildRedirect($this->action('page_template_deleted'));
                }

                return $this->buildRedirect($this->action());
            }
        }

        $this->action = 'edit';
        $this->edit($pTemplateID);
    }

    public function edit($pTemplateID = false)
    {
        $this->set('icons', PageTemplate::getIcons());
        $template = PageTemplate::getByID($pTemplateID);
        if (!is_object($template)) {
            throw new Exception(t('Invalid page template'));
        }

        $this->set('template', $template);
    }

    public function page_template_added()
    {
        $this->set('success', t('Page template added successfully.'));
        $this->view();
    }

    public function page_template_deleted()
    {
        $this->set('success', t('Page template deleted successfully.'));
        $this->view();
    }

    public function page_template_updated()
    {
        $this->set('success', t('Page template updated successfully.'));
        $this->view();
    }

    public function update()
    {
        $pt = PageTemplate::getByID($pTemplateID = $this->request->get('pTemplateID'));
        $pTemplateName = $this->request->request->get('pTemplateName');
        $pTemplateHandle = $this->request->request->get('pTemplateHandle');
        $pTemplateIcon = $this->request->request->get('pTemplateIcon');

        $valt = $this->app->make('helper/validation/token');
        $vs = $this->app->make('helper/validation/strings');

        if (!is_object($pt)) {
            $this->error->add(t('Invalid page template object.'));
        }

        if (!$pTemplateHandle) {
            $this->error->add(t('Handle required.'));
        } elseif (!$vs->handle($pTemplateHandle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }

        if (!$pTemplateName) {
            $this->error->add(t('Name required.'));
        } elseif (preg_match('/[<>;{}?"`]/i', $pTemplateName)) {
            $this->error->add(t('Invalid characters in page template name.'));
        }

        if (!$pTemplateIcon) {
            $this->error->add(t('Icon required.'));
        } elseif (preg_match('/[<>;{}?"`]/i', $pTemplateIcon)) {
            $this->error->add(t('Invalid characters in icon template name.'));
        }

        if (!$valt->validate('update_page_template')) {
            $this->error->add($valt->getErrorMessage());
        }

        if (!$this->error->has()) {
            $pt->update($pTemplateHandle, $pTemplateName, $pTemplateIcon);

            return $this->buildRedirect($this->action('page_template_updated'));
        }

        $this->edit($pTemplateID);
    }
}

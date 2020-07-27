<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages\Templates;

use Concrete\Core\Page\Controller\DashboardPageController;
use PageTemplate;

class Add extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();

        $this->set('icons', PageTemplate::getIcons());
    }

    public function add_page_template()
    {
        $pTemplateName = $this->request->request->get('pTemplateName');
        $pTemplateHandle = $this->request->request->get('pTemplateHandle');
        $pTemplateIcon = $this->request->request->get('pTemplateIcon');

        $valt = $this->app->make('helper/validation/token');
        $vs = $this->app->make('helper/validation/strings');

        if (!$pTemplateHandle) {
            $this->error->add(t('Handle required.'));
        } elseif (!$vs->handle($pTemplateHandle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }

        if (!$pTemplateName) {
            $this->error->add(t('Name required.'));
        } elseif (preg_match('/[<>{};?"`]/i', $pTemplateName)) {
            $this->error->add(t('Invalid characters in page template name.'));
        }

        if (!$pTemplateIcon) {
            $this->error->add(t('Icon required.'));
        } elseif (preg_match('/[<>;{}?"`]/i', $pTemplateIcon)) {
            $this->error->add(t('Invalid characters in icon template name.'));
        }

        if (!$valt->validate('add_page_template')) {
            $this->error->add($valt->getErrorMessage());
        }

        if (!$this->error->has()) {
            $pt = PageTemplate::add($pTemplateHandle, $pTemplateName, $pTemplateIcon);

            return $this->buildRedirect('/dashboard/pages/templates/page_template_added');
        }
    }
}

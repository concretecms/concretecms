<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Summary\Template\Command\DisableCustomPageSummaryTemplatesCommand;
use Concrete\Core\Page\Summary\Template\Command\EnableCustomPageSummaryTemplatesCommand;

class SummaryTemplates extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/summary_templates/choose';

    protected function canAccess()
    {
        return $this->permissions->canEditPageProperties();
    }

    public function view()
    {
        $pageTemplates = $this->page->getSummaryTemplates();
        $selectedTemplateIDs = [];
        $templates = [];
        $selectedTemplates = $this->page->getCustomSelectedSummaryTemplates();
        if ($selectedTemplates) {
            foreach($selectedTemplates as $selectedTemplate) {
                $selectedTemplateIDs[] = $selectedTemplate->getID();
            }
        }
        if ($pageTemplates) {
            foreach($pageTemplates as $pageTemplate) {
                $templates[] = $pageTemplate;
            }
        }
        $this->set('memberIdentifier', $this->page->getCollectionID());
        $this->set('categoryHandle', 'page');
        $this->set('object', $this->page);
        $this->set('templates', $templates);
        $this->set('selectedTemplateIDs', $selectedTemplateIDs);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            if ($this->request->request->get('hasCustomSummaryTemplates')) {
                $command = new EnableCustomPageSummaryTemplatesCommand($this->page->getCollectionID());
                $keys = array_keys($this->request->request->all());
                foreach($keys as $key) {
                    if (substr($key, 0, 8) === 'template') {
                        $templateIDs[] = substr($key, 9);
                    }
                }
                if ($templateIDs) {
                    $command->setTemplateIDs($templateIDs);
                }
            } else {
                $command = new DisableCustomPageSummaryTemplatesCommand($this->page->getCollectionID());
            }
            $this->app->executeCommand($command);

            $r = new EditResponse();
            $r->setPage($this->page);
            $r->setTitle(t('Page Updated'));
            $r->setMessage(t('Summary templates settings saved.'));
            $r->outputJSON();
        }
    }
}

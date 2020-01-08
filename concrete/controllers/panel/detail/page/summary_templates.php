<?php
namespace Concrete\Controller\Panel\Detail\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Summary\Template\Command\DisableCustomPageSummaryTemplatesCommand;
use Concrete\Core\Page\Summary\Template\Command\EnableCustomPageSummaryTemplatesCommand;

class SummaryTemplates extends BackendInterfacePageController
{
    protected $viewPath = '/panels/details/page/summary_templates';

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
                $templates[] = $pageTemplate->getTemplate();
            }
        }
        $this->set('templates', $templates); 
        $this->set('selectedTemplateIDs', $selectedTemplateIDs);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            if ($this->request->request->get('hasCustomSummaryTemplates')) {
                $command = new EnableCustomPageSummaryTemplatesCommand($this->page->getCollectionID());
                $templateIDs = $this->request->request->get('templateIDs');
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

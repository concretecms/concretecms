<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\CreateBoardCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Template;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Add extends DashboardSitePageController
{
    
    public function on_start()
    {
        $permissions = new Checker();
        if (!$permissions->canAddBoard()) {
            return $this->redirect('/dashboard/boards/boards');
        }
        parent::on_start();
    }

    public function view()
    {
        $templates = ['' => t('** Select a Template')];
        foreach($this->entityManager->getRepository(Template::class)->findAll() as $template) {
            $templates[$template->getId()] = $template->getName();
        }
        $this->set('templates', $templates);
        $service = $this->app->make(InstallationService::class);
        $this->set('multisite', $service->isMultisiteEnabled());
    }

    protected function validateBoardRequest()
    {
        $security = new SanitizeService();
        $strings = $this->app->make(Strings::class);
        $name = $security->sanitizeString($this->post('boardName'));
        if (!$this->token->validate('submit')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        if (!$strings->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your board.'));
        }

        $template = null;
        if ($this->request->request->has('templateID')) {
            $template = $this->entityManager->find(Template::class, $this->request->request->get('templateID'));
        }

        if (!$template) {
            $this->error->add(t('You must specify a valid template for your board.'));
        }
        
        return [$name, $template];
    }
    
    public function submit()
    {
        list($name, $template) = $this->validateBoardRequest();
        if (!$this->error->has()) {
            $command = new CreateBoardCommand();
            if (empty($this->request->request->get('sharedBoard'))) {
                $command->setSite($this->getSite());
            }
            $sortBy = 'relevant_date_asc';
            if (in_array($this->request->request->get('sortBy'), ['relevant_date_desc'])) {
                $sortBy = $this->request->request->get('sortBy');
            }
            $command->setSortBy($sortBy);
            $command->setName($name);
            $command->setTemplate($template);
            $board = $this->executeCommand($command);

            $this->redirect('/dashboard/boards/details', 'view', $board->getBoardID());
        }
        $this->view();
    }
}

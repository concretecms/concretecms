<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Template;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Add extends DashboardSitePageController
{
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

    public function submit()
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
        
        if (!$this->error->has()) {
            $board = new Board();
            $site = $this->getSite();
            if ($this->request->request->has('sharedBoard') && $this->request->request->get('sharedBoard') === '1') {
                $site = null;
            }
            $board->setSite($this->getSite());
            $board->setBoardName($name);
            $board->setTemplate($template);
            $this->entityManager->persist($board);
            $this->entityManager->flush();
            $this->redirect('/dashboard/boards/details', 'view', $board->getBoardID());
        }
        $this->view();
    }
}

<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Command\DeleteBoardInstanceCommand;
use Concrete\Core\Board\Command\RefreshBoardInstanceCommand;
use Concrete\Core\Board\Command\RegenerateBoardInstanceCommand;
use Concrete\Core\Board\Instance\Renderer;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\View\View;
use Concrete\Theme\Concrete\PageTheme;
use Symfony\Component\HttpFoundation\Response;

class Instances extends DashboardSitePageController
{

    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        return $r->findOneByBoardID($id);
    }
    
    public function generate_instance($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('generate_instance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $generate = new CreateBoardInstanceCommand();
                $generate->setBoard($board);
                $this->executeCommand($generate);
                $this->flash('success', t('Board instance created.'));
                return $this->redirect('/dashboard/boards/instances/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function refresh_instance($id = null)
    {
        $instance = $this->entityManager->find(Instance::class, $id);
        if (is_object($instance)) {
            if (!$this->token->validate('refresh_instance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $board = $instance->getBoard();
                $command = new RefreshBoardInstanceCommand();
                $command->setInstance($instance);
                $this->executeCommand($command);
                $this->flash('success', t('Board instance refreshed.'));
                return $this->redirect('/dashboard/boards/instances/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function regenerate_instance($id = null)
    {
        $instance = $this->entityManager->find(Instance::class, $id);
        if (is_object($instance)) {
            if (!$this->token->validate('regenerate_instance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $board = $instance->getBoard();
                $command = new RegenerateBoardInstanceCommand();
                $command->setInstance($instance);
                $this->executeCommand($command);
                $this->flash('success', t('Board instance regenerated.'));
                return $this->redirect('/dashboard/boards/instances/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


    public function view_instance($id = null)
    {
        $instance = $this->entityManager->find(Instance::class, $id);
        if ($instance) {
            /**
             * @var $instance Instance
             */
            $site = $instance->getBoard()->getSite();
            if ($site) {
                $home = $site->getSiteHomePageObject();
                $theme = $home->getCollectionThemeObject();
            } else {
                $theme = PageTheme::getSiteTheme();
            }

            $this->set('renderer',$this->app->make(Renderer::class, ['theme' => $theme]));
            $this->set('instance', $instance);
            
            $this->setTheme($theme);
            $this->render('/dashboard/boards/instances/view_instance');

        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


    public function delete_instance($boardInstanceID = null)
    {
        $instance = $this->entityManager->find(Instance::class, $boardInstanceID);
        if ($instance) {
            $board = $instance->getBoard();
            if (!$this->token->validate('delete_instance')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                $resetCommand = new DeleteBoardInstanceCommand();
                $resetCommand->setInstance($instance);
                $this->executeCommand($resetCommand);

                $this->flash('success', t('Board instance removed successfully.'));
                return $this->redirect('/dashboard/boards/instances', 'view', $board->getBoardID());
            }
            $this->view($board->getId());
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


    public function view($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            $instances = $this->entityManager->getRepository(Instance::class)
                ->findByBoard($board);
            $this->set('instances', $instances);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}

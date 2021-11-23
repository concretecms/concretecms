<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Instances;

use Concrete\Core\Board\Command\AddContentToBoardInstanceCommand;
use Concrete\Core\Board\Command\ClearBoardInstanceDataPoolCommand;
use Concrete\Core\Board\Command\DeleteBoardInstanceCommand;
use Concrete\Core\Board\Command\PopulateBoardInstanceDataPoolCommand;
use Concrete\Core\Board\Command\RefreshBoardInstanceCommand;
use Concrete\Core\Board\Command\RegenerateBoardInstanceCommand;
use Concrete\Core\Board\Instance\Renderer;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Details extends DashboardPageController
{

    /**
     * @param $id
     * @return Instance
     */
    protected function getInstance($id)
    {
        $r = $this->entityManager->getRepository(Instance::class);
        $instance = $r->findOneByBoardInstanceID($id);
        if ($instance) {
            $board = $instance->getBoard();
            if ($board) {
                $checker = new Checker($board);
                if ($checker->canEditBoardSettings()) {
                    return $instance;
                }
            }
        }
    }

    public function refresh_pool($id = null)
    {
        $instance = $this->getInstance($id);
        if (is_object($instance)) {
            if (!$this->token->validate('refresh_pool')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $clear = new ClearBoardInstanceDataPoolCommand();
                $clear->setInstance($instance);
                $populate = new PopulateBoardInstanceDataPoolCommand();
                $populate->setInstance($instance);
                $this->executeCommand($clear);
                $this->executeCommand($populate);
                $this->flash('success', t('Board data pool refreshed.'));
                return $this->redirect('/dashboard/boards/instances/details/', 'view', $instance->getBoardInstanceID());
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
                $command = new RefreshBoardInstanceCommand();
                $command->setInstance($instance);
                $this->executeCommand($command);
                $this->flash('success', t('Board instance refreshed.'));
                return $this->redirect('/dashboard/boards/instances/details', 'view', $instance->getBoardInstanceID());
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
                $command = new RegenerateBoardInstanceCommand();
                $command->setInstance($instance);
                $this->executeCommand($command);

                $this->flash('success', t('Board instance regenerated.'));
                return $this->redirect('/dashboard/boards/instances/details', 'view', $instance->getBoardInstanceID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function add_content($id = null)
    {
        $instance = $this->entityManager->find(Instance::class, $id);
        if (is_object($instance)) {
            if (!$this->token->validate('add_content')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $command = new AddContentToBoardInstanceCommand();
                $command->setInstance($instance);
                $this->executeCommand($command);
                $this->flash('success', t('Content added to instance.'));
                return $this->redirect('/dashboard/boards/instances/details', 'view', $instance->getBoardInstanceID());
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
            $site = $instance->getsite();
            $home = $site->getSiteHomePageObject();
            $theme = $home->getCollectionThemeObject();

            $renderer = $this->app->make(Renderer::class);
            $renderer->setEnableEditing(true);
            $this->set('renderer', $renderer);
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
        $instance = $this->getInstance($id);
        if (is_object($instance)) {
            $configuredSources = $this->entityManager->getRepository(ConfiguredDataSource::class)
                ->findByBoard($instance->getBoard());
            $this->set('itemRepository', $this->entityManager->getRepository(InstanceItem::class));
            $this->set('configuredSources', $configuredSources);
            $this->set('instance', $instance);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

}

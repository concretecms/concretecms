<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\Container\Command\ContainerCommandValidator;
use Concrete\Core\Page\Container\Command\DeleteContainerCommand;
use Concrete\Core\Page\Container\Command\UpdateContainerCommand;
use Concrete\Core\Page\Controller\DashboardPageController;

class Containers extends DashboardPageController
{
    public function view()
    {
        $this->set(
            'containers',
            $this->entityManager->getRepository(Container::class)
                ->findBy([], ['containerName' => 'asc'])
        );
    }

    public function edit($containerID = null)
    {
        $container = $this->getContainer($containerID);
        $this->set('container', $container);
        $this->set('tokenMessage', 'update_container');
        $this->render('/dashboard/pages/containers/form');
    }

    public function delete_container($containerID = null)
    {
        if ($this->token->validate('delete_container')) {
            $container = $this->getContainer($containerID);
            $command = new DeleteContainerCommand($container);
            $this->executeCommand($command);
            $this->flash('success', t('Container removed successfully.'));

            return $this->buildRedirect('/dashboard/pages/containers');
        }

        $this->error->add($this->token->getErrorMessage());

        $this->edit($containerID);
    }

    public function update_container($containerID = null)
    {
        if ($this->token->validate('update_container')) {
            $container = $this->getContainer($containerID);
            $container->setContainerName($this->request->request->get('containerName'));
            $container->setContainerHandle($this->request->request->get('containerHandle'));
            $container->setContainerIcon($this->request->request->get('containerIcon'));
            $command = new UpdateContainerCommand($container);
            $validator = $this->app->make(ContainerCommandValidator::class);

            $errorList = $validator->validate($command);

            if ($errorList->has()) {
                $this->error->add($errorList);
            } else {
                $this->executeCommand($command);
                $this->flash('success', t('Container saved successfully.'));

                return $this->buildRedirect('/dashboard/pages/containers');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        $this->edit($containerID);
    }

    protected function getContainer($containerID = null)
    {
        $container = null;
        if ($containerID) {
            $container = $this->entityManager->find(Container::class, $containerID);
        }

        if (!$container) {
            return $this->buildRedirect('/dashboard/pages/containers');
        }

        return $container;
    }
}

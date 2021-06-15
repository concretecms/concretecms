<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages\Containers;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\Container\Command\AddContainerCommand;
use Concrete\Core\Page\Container\Command\ContainerCommandValidator;
use Concrete\Core\Page\Controller\DashboardPageController;

class Add extends DashboardPageController
{
    public function view()
    {
        $this->set('tokenMessage', 'add_container');
        $this->render('/dashboard/pages/containers/form');
    }

    public function add_container()
    {
        if ($this->token->validate('add_container')) {
            $container = new Container();
            $container->setContainerName($this->request->request->get('containerName'));
            $container->setContainerHandle($this->request->request->get('containerHandle'));
            $container->setContainerIcon($this->request->request->get('containerIcon'));
            $command = new AddContainerCommand($container);
            $validator = $this->app->make(ContainerCommandValidator::class);

            $errorList = $validator->validate($command);

            if ($errorList->has()) {
                $this->error->add($errorList);
            } else {
                $this->executeCommand($command);
                $this->flash('success', t('Container added successfully.'));

                return $this->buildRedirect('/dashboard/pages/containers');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        $this->view();
    }
}

<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Entity\Command\Command;
use Concrete\Core\Page\Controller\DashboardPageController;

class Commands extends DashboardPageController
{
    public function view()
    {
        $commands = $this->entityManager->getRepository(Command::class)
            ->findAll();
        $this->set('commands', $commands);
    }
}

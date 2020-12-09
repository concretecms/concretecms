<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Page\Controller\DashboardPageController;

class Processes extends DashboardPageController
{

    public function view($processID = null)
    {
        $r = $this->entityManager->getRepository(Process::class);
        $this->set('processes', $r->findAll());
    }

}

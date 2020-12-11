<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Process\Command\DeleteProcessCommand;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Page\Controller\DashboardPageController;

class Processes extends DashboardPageController
{

    public function view($processID = null)
    {
        $r = $this->entityManager->getRepository(Process::class);
        $this->set('processes', $r->findAll());
    }

    public function delete($token = null)
    {
        $process = $this->entityManager->find(
            Process::class,
            $this->request->request->get('processId')
        );
        if (!$this->token->validate('delete', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$process) {
            $this->error->add(t('Invalid process ID'));
        }
        if (!$this->error->has()) {
            $this->executeCommand(new DeleteProcessCommand($process->getID()));
            $this->flash('success', t('Process Deleted'));
            return $this->buildRedirect(['/dashboard/system/automation/processes']);
        }

        $this->view();
    }

}

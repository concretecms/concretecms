<?php
namespace Concrete\Controller\Element;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Notification\Mercure\MercureService;

class ProcessList extends ElementController
{

    /**
     * @var array
     */
    protected $processes;

    /**
     * @param array $processes
     */
    public function setProcesses(array $processes): void
    {
        $this->processes = $processes;
    }


    public function getElement()
    {
        return 'process_list';
    }

    public function view()
    {
        $token = $this->app->make('token');
        $mercureService = $this->app->make(MercureService::class);
        $eventSource = null;
        $poll = false;
        if ($mercureService->isEnabled()) {
            $eventSource = $mercureService->getPublisherUrl();
        } else {
            $poll = true;
        }
        $this->set('poll', $poll);
        $this->set('pollToken', $token->generate('poll'));
        $this->set('eventSource', $eventSource);
        $this->set('processes', $this->processes);
        $this->set('id', uniqid());
    }


}

<?php

namespace Concrete\Core\Command\Scheduler\Response;

use Concrete\Core\Entity\Command\ScheduledTask;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class HttpResponseFactory
{

    const STATUS_SCHEDULED = 'task_scheduled';

    /**
     * @var Session
     */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function createResponse(ScheduledTask $task)
    {
        $this->session->getFlashBag()->add('page_message', ['message', t('Task scheduled successfully.')]);
        return new JsonResponse(['status' => self::STATUS_SCHEDULED, 'scheduledTask' => $task]);
    }
}

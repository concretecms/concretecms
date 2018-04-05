<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Foundation\Queue\Response\QueueProgressResponse;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Foundation\Queue\QueueService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Queue extends AbstractController
{

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var QueueService
     */
    protected $service;

    public function __construct(Token $token, QueueService $service)
    {
        $this->token = $token;
        $this->service = $service;
        parent::__construct();
    }

    public function monitor($queue, $token)
    {
        if ($this->token->validate($queue, $token)) {
            $queue = $this->service->get($queue);
            if ($queue) {
                $this->service->consumeFromPoll($queue);
                return new QueueProgressResponse($queue);
            }
        }
        throw new \Exception(t('Access Denied'));
    }

}

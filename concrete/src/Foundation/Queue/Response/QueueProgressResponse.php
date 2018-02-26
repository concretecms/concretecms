<?php

namespace Concrete\Core\Foundation\Queue\Response;

use Bernard\Queue;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class QueueProgressResponse extends JsonResponse
{

    protected $name;

    public function __construct(Queue $queue)
    {
        $count = $queue->count();
        $data = [
            'queue' => (string) $queue,
            'remaining' => $count,
        ];
        parent::__construct($data);
    }

}

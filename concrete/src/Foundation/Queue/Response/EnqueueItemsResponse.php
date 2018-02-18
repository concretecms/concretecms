<?php

namespace Concrete\Core\Foundation\Queue\Response;

use Bernard\Queue;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class EnqueueItemsResponse extends JsonResponse
{

    protected $name;

    public function __construct(Queue $queue)
    {
        $validator = new Token();
        $data = [
            'queue' => (string) $queue,
            'token' => $validator->generate((string) $queue)
        ];
        parent::__construct($data);
    }

}

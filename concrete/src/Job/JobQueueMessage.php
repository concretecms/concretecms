<?php
namespace Concrete\Core\Job;

/**
 * A wrapper class for data coming into processQueueItem so transitioning to the version 9 queue is mostly pain-free.
 * Class JobQueueMessage
 * @package Concrete\Core\Job
 */
class JobQueueMessage
{

    public $body;

    /**
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }


}
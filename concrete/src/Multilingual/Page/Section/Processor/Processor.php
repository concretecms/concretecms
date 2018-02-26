<?php
namespace Concrete\Core\Multilingual\Page\Section\Processor;

use Concrete\Core\Foundation\Processor\ProcessorQueue as ProcessorQueue;
use Concrete\Core\Foundation\Processor\TargetInterface;
use Concrete\Core\Foundation\Queue\QueueService;

defined('C5_EXECUTE') or die("Access Denied.");

class Processor extends ProcessorQueue
{
    protected $section;

    public function __construct(QueueService $queue, TargetInterface $target)
    {
        parent::__construct($queue, $target);
        $this->setQueue($queue->get('multilingual_section_processor'));
        $this->registerTask(new ReplaceContentLinksTask());
        $this->registerTask(new ReplaceBlockPageRelationsTask());
    }
}

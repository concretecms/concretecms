<?php

namespace Concrete\Core\Multilingual\Page\Section\Processor;

use Concrete\Core\Foundation\Processor\Processor as FoundationProcessor;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Multilingual\Page\Section\Section;

defined('C5_EXECUTE') or die("Access Denied.");

class Processor extends FoundationProcessor
{
    protected $section;

    public function __construct(Section $section)
    {
        parent::__construct(new MultilingualProcessorTarget($section));
        $this->setQueue(Queue::get('multilingual_section_processor'));
        $this->registerTask(new ReplaceContentLinksTask());
        $this->registerTask(new ReplaceBlockPageRelationsTask());
    }

}

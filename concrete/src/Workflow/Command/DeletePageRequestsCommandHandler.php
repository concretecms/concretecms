<?php

namespace Concrete\Core\Workflow\Command;

use Concrete\Core\Page\Page;
use Concrete\Core\Workflow\BasicWorkflow;
use Concrete\Core\Workflow\Progress\BasicData;
use Concrete\Core\Workflow\Progress\PageProgress;

class DeletePageRequestsCommandHandler
{
    public function __invoke(DeletePageRequestsCommand $command)
    {
        $c = Page::getByID($command->getPageID());
        $progresses = PageProgress::getList($c);
        /** @var PageProgress $progress */
        foreach ($progresses as $progress) {
            $workflow = $progress->getWorkflowObject();
            if ($workflow instanceof BasicWorkflow) {
                $progressData = new BasicData($progress);
                $progressData->delete();
            }
            $progress->delete();
        }
    }
}

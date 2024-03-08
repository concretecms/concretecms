<?php

namespace Concrete\Core\Workflow\Command;

use Concrete\Core\Page\Page;
use Concrete\Core\Workflow\BasicWorkflow;
use Concrete\Core\Workflow\Progress\BasicData;
use Concrete\Core\Workflow\Progress\PageProgress;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Concrete\Core\Workflow\Request\UnapprovePageRequest;

class DeletePageVersionRequestsCommandHandler
{
    public function __invoke(DeletePageVersionRequestsCommand $command)
    {
        $cID = $command->getPageID();
        $cvID = $command->getVersionID();

        $c = Page::getByID($cID);
        $progresses = PageProgress::getList($c);
        /** @var PageProgress $progress */
        foreach ($progresses as $progress) {
            $request = $progress->getWorkflowRequestObject();
            if ($request instanceof ApprovePageRequest || $request instanceof UnapprovePageRequest) {
                if ($request->getRequestedVersionID() == $cvID) {
                    $workflow = $progress->getWorkflowObject();
                    if ($workflow instanceof BasicWorkflow) {
                        $progressData = new BasicData($progress);
                        $progressData->delete();
                    }
                    $progress->delete();
                }
            }
        }
    }
}

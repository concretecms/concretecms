<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\PageProgress as PageWorkflowProgress;

$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);

$obj = new stdClass();
if ($_REQUEST['task'] == 'save_workflow_progress' && Loader::helper("validation/token")->validate('save_workflow_progress')) {
    $wp = PageWorkflowProgress::getByID($_REQUEST['wpID']);
    if (is_object($wp)) {
        $wf = $wp->getWorkflowObject();
        $form = Loader::helper('form');
        $obj->wpID = $wp->getWorkflowProgressID();
        if ($wf->canApproveWorkflowProgressObject($wp)) {
            $task = WorkflowProgress::getRequestedTask();
            if ($task) {
                $r = $wp->runTask($task, $_POST);
                if (($r instanceof WorkflowProgressResponse) && $r->getWorkflowProgressResponseURL() != '') {
                    $obj->redirect = (string) $r->getWorkflowProgressResponseURL();
                } else {
                    $obj->redirect = (string) URL::to($c);
                }
            }
        }
    }
}

echo Loader::helper('json')->encode($obj);

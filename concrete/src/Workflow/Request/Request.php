<?php

namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Workflow\EmptyWorkflow;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\SkippedResponse;
use Symfony\Component\EventDispatcher\GenericEvent;

abstract class Request extends ConcreteObject
{
    protected $currentWP;

    protected $uID;

    protected $wrStatusNum = 0;

    protected $wrID;

    public function __construct($pk)
    {
        $this->pkID = $pk->getPermissionKeyID();
    }

    public function getWorkflowRequestStatusNum()
    {
        return $this->wrStatusNum;
    }

    public function getWorkflowRequestID()
    {
        return $this->wrID;
    }

    public function getWorkflowRequestPermissionKeyID()
    {
        return $this->pkID;
    }

    public function getWorkflowRequestPermissionKeyObject()
    {
        return PermissionKey::getByID($this->pkID);
    }

    public function setCurrentWorkflowProgressObject(WorkflowProgress $wp)
    {
        $this->currentWP = $wp;
    }

    public function getCurrentWorkflowProgressObject()
    {
        return $this->currentWP;
    }

    public function setRequesterUserID($uID)
    {
        $this->uID = $uID;
    }

    public function getRequesterUserID()
    {
        return $this->uID;
    }

    public function getRequesterUserObject()
    {
        return UserInfo::getByID($this->uID);
    }

    public static function getByID($wrID)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $wrObject = $db->fetchOne('select wrObject from WorkflowRequestObjects where wrID = ?', [$wrID]);
        if ($wrObject) {
            return unserialize($wrObject);
        }
    }

    public function delete()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $db->delete('WorkflowRequestObjects', ['wrID' => $this->wrID]);
    }

    public function save()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        if (!$this->wrID) {
            $wrObject = '';
            $db->insert('WorkflowRequestObjects', ['wrObject' => $wrObject]);
            $this->wrID = $db->lastInsertId();
        }
        $wrObject = serialize($this);
        $db->update('WorkflowRequestObjects', ['wrObject' => $wrObject], ['wrID' => $this->wrID]);
    }

    abstract public function addWorkflowProgress(\Concrete\Core\Workflow\Workflow $wf);

    abstract public function getWorkflowRequestDescriptionObject();

    abstract public function getWorkflowRequestStyleClass();

    abstract public function getWorkflowRequestApproveButtonText();

    abstract public function getWorkflowRequestApproveButtonClass();

    abstract public function getWorkflowRequestApproveButtonInnerButtonRightHTML();

    public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp)
    {
        return [];
    }

    public function runTask($task, WorkflowProgress $wp)
    {
        if (method_exists($this, $task)) {
            if ($task == 'approve') {
                // we check to see if any other outstanding workflowprogress requests have this id
                // if they don't we proceed
                $app = Application::getFacadeApplication();
                /** @var Connection $db */
                $db = $app->make(Connection::class);
                $num = $db->fetchOne(
                    'select count(wpID) as total from WorkflowProgress where wpID <> ? and wrID = ? and wpIsCompleted = 0',
                    [
                        $wp->getWorkflowProgressID(),
                        $this->getWorkflowRequestID(),
                    ]
                );
                if ($num == 0) {
                    return call_user_func_array([$this, $task], [$wp]);
                }
            } else {
                return call_user_func_array([$this, $task], [$wp]);
            }
        }
    }

    public function getRequesterComment()
    {
        return false;
    }

    abstract public function getRequestIconElement();

    /**
     * Triggers a workflow request, queries a permission key to see what workflows are attached to it
     * and initiates them.
     *
     * @param PermissionKey $pk
     *
     * @return WorkflowProgress|null
     */
    protected function triggerRequest(PermissionKey $pk)
    {
        if (!$this->wrID) {
            $this->save();
        }

        if (!$pk->canPermissionKeyTriggerWorkflow()) {
            throw new \Exception(t('This permission key cannot start a workflow.'));
        }

        $app = Application::getFacadeApplication();

        $pa = $pk->getPermissionAccessObject();
        $skipWorkflow = true;
        if (is_object($pa)) {
            $workflows = $pa->getWorkflows();
            foreach ($workflows as $wf) {
                if ($wf->validateTrigger($this)) {
                    $wp = $this->addWorkflowProgress($wf);
                    $response = $wp->getWorkflowProgressResponseObject();
                    if ($response instanceof SkippedResponse) {
                        // Since the response was skipped, we delete the workflow progress operation and keep moving.
                        $wp->delete();
                    } else {
                        $skipWorkflow = false;
                    }
                    $event = new GenericEvent();
                    $event->setArgument('progress', $wp);
                    $app['director']->dispatch('workflow_triggered', $event);
                }
            }
        }

        if ($skipWorkflow) {
            $defaultWorkflow = new EmptyWorkflow();
            $wp = $this->addWorkflowProgress($defaultWorkflow);

            $event = new GenericEvent();
            $event->setArgument('progress', $wp);
            $app['director']->dispatch('workflow_triggered', $event);

            return $wp->getWorkflowProgressResponseObject();
        }
    }
}

<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Foundation\Object;
use Concrete\Core\User\UserInfo;
use Symfony\Component\EventDispatcher\GenericEvent;
use Workflow;
use Concrete\Core\Workflow\EmptyWorkflow;
use Database;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use PermissionKey;
use Events;

abstract class Request extends Object
{
    protected $currentWP;
    protected $uID;
    protected $wrStatusNum = 0;
    protected $wrID = null;

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
        $db = Database::connection();
        $wrObject = $db->getOne('select wrObject from WorkflowRequestObjects where wrID = ?', array($wrID));
        if ($wrObject) {
            $wr = unserialize($wrObject);

            return $wr;
        }
    }

    public function delete()
    {
        $db = Database::connection();
        $db->Execute('delete from WorkflowRequestObjects where wrID = ?', array($this->wrID));
    }

    public function save()
    {
        $db = Database::connection();
        if (!$this->wrID) {
            $wrObject = '';
            $db->Execute('insert into WorkflowRequestObjects (wrObject) values (?)', array($wrObject));
            $this->wrID = $db->Insert_ID();
        }
        $wrObject = serialize($this);
        $db->Execute('update WorkflowRequestObjects set wrObject = ? where wrID = ?', array($wrObject, $this->wrID));
    }

    /**
     * Triggers a workflow request, queries a permission key to see what workflows are attached to it
     * and initiates them.
     *
     * @param \PermissionKey $pk
     *
     * @return optional WorkflowProgress
     */
    protected function triggerRequest(\PermissionKey $pk)
    {
        if (!$this->wrID) {
            $this->save();
        }

        if (!$pk->canPermissionKeyTriggerWorkflow()) {
            throw new \Exception(t('This permission key cannot start a workflow.'));
        }

        $pa = $pk->getPermissionAccessObject();
        $workflows = array();
        $workflowsStarted = 0;
        if (is_object($pa)) {
            $workflows = $pa->getWorkflows();
            foreach ($workflows as $wf) {
                if ($wf->validateTrigger($this)) {
                    $wp = $this->addWorkflowProgress($wf);
                    ++$workflowsStarted;

                    $event = new GenericEvent();
                    $event->setArgument('progress', $wp);
                    Events::dispatch('workflow_triggered', $event);
                }
            }
        }

        if (isset($wp)) {
            return $wp->getWorkflowProgressResponseObject();
        }

        if ($workflowsStarted == 0) {
            $defaultWorkflow = new EmptyWorkflow();
            $wp = $this->addWorkflowProgress($defaultWorkflow);

            $event = new GenericEvent();
            $event->setArgument('progress', $wp);
            Events::dispatch('workflow_triggered', $event);

            return $wp->getWorkflowProgressResponseObject();
        }
    }

    abstract public function addWorkflowProgress(\Concrete\Core\Workflow\Workflow $wf);
    abstract public function getWorkflowRequestDescriptionObject();
    abstract public function getWorkflowRequestStyleClass();
    abstract public function getWorkflowRequestApproveButtonText();
    abstract public function getWorkflowRequestApproveButtonClass();
    abstract public function getWorkflowRequestApproveButtonInnerButtonRightHTML();

    public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp)
    {
        return array();
    }

    public function runTask($task, WorkflowProgress $wp)
    {
        if (method_exists($this, $task)) {
            if ($task == 'approve') {
                // we check to see if any other outstanding workflowprogress requests have this id
                // if they don't we proceed
                $db = Database::connection();
                $num = $db->GetOne(
                    'select count(wpID) as total from WorkflowProgress where wpID <> ? and wrID = ? and wpIsCompleted = 0',
                    array(
                        $wp->getWorkflowProgressID(),
                        $this->getWorkflowRequestID(),
                    )
                );
                if ($num == 0) {
                    $wpr = call_user_func_array(array($this, $task), array($wp));

                    return $wpr;
                }
            } else {
                $wpr = call_user_func_array(array($this, $task), array($wp));

                return $wpr;
            }
        }
    }

    public function getRequesterComment()
    {
        return false;
    }

    abstract public function getRequestIconElement();

}

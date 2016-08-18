<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Entity\Notification\WorkflowProgressNotification;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Request\Request as WorkflowRequest;
use Concrete\Core\Workflow\EmptyWorkflow;
use Concrete\Core\Workflow\Progress\Category as WorkflowProgressCategory;
use Concrete\Core\Package\PackageList;
use Database;
use Core;
use Events;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Base class for workflow progresses.
 *
 * @method static Progress add(string $wpCategoryHandle, Workflow $wf, WorkflowRequest $wr) Deprecated method. Use Progress::create instead.
 */
abstract class Progress extends Object implements SubjectInterface
{
    protected $wrID = null;
    protected $wpID;
    protected $wpDateAdded;
    protected $wfID;
    protected $response;
    protected $wpDateLastAction;

    public function getNotificationDate()
    {
        return \Core::make('date')->toDateTime($this->wpDateAdded);
    }

    public function getUsersToExcludeFromNotification()
    {
        return array();
    }

    /**
     * Gets the Workflow object attached to this WorkflowProgress object.
     *
     * @return Workflow
     */
    public function getWorkflowObject()
    {
        if ($this->wfID > 0) {
            $wf = Workflow::getByID($this->wfID);
        } else {
            $wf = new EmptyWorkflow();
        }

        return $wf;
    }

    /**
     * Gets an optional WorkflowResponse object. This is set in some cases.
     */
    public function getWorkflowProgressResponseObject()
    {
        return $this->response;
    }

    public function setWorkflowProgressResponseObject($obj)
    {
        $this->response = $obj;
    }

    /**
     * Gets the date of the last action.
     */
    public function getWorkflowProgressDateLastAction()
    {
        return $this->wpDateLastAction;
    }

    /**
     * Gets the ID of the progress object.
     */
    public function getWorkflowProgressID()
    {
        return $this->wpID;
    }

    /**
     * Gets the ID of the progress object.
     */
    public function getWorkflowProgressCategoryHandle()
    {
        return $this->wpCategoryHandle;
    }

    /**
     * Get the category ID.
     */
    public function getWorkflowProgressCategoryID()
    {
        return $this->wpCategoryID;
    }

    /**
     * Gets the date the WorkflowProgress object was added.
     *
     * @return datetime
     */
    public function getWorkflowProgressDateAdded()
    {
        return $this->wpDateAdded;
    }

    /**
     * Get the WorkflowRequest object for the current WorkflowProgress object.
     *
     * @return WorkflowRequest
     */
    public function getWorkflowRequestObject()
    {
        if ($this->wrID > 0) {
            $cat = WorkflowProgressCategory::getByID($this->wpCategoryID);
            $handle = $cat->getWorkflowProgressCategoryHandle();
            $class = '\\Core\\Workflow\\Request\\' . Core::make('helper/text')->camelcase($handle) . 'Request';
            $pkHandle = $cat->getPackageHandle();
            $class = core_class($class, $pkHandle);
            $wr = $class::getByID($this->wrID);
            if (is_object($wr)) {
                $wr->setCurrentWorkflowProgressObject($this);

                return $wr;
            }
        }
    }

    public static function __callStatic($name, $arguments)
    {
        if (strcasecmp($name, 'add') === 0) {
            return call_user_func_array('static::create', $arguments);
        }
        trigger_error("Call to undefined method ".__CLASS__."::$name()", E_USER_ERROR);
    }

    /**
     * Creates a WorkflowProgress object (which will be assigned to a Page, File, etc... in our system.
     *
     * @param string $wpCategoryHandle
     * @param Workflow $wf
     * @param WorkflowRequest $wr
     *
     * @return self
     */
    public static function create($wpCategoryHandle, Workflow $wf, WorkflowRequest $wr)
    {
        $db = Database::connection();
        $wpDateAdded = Core::make('helper/date')->getOverridableNow();
        $wpCategoryID = $db->fetchColumn('select wpCategoryID from WorkflowProgressCategories where wpCategoryHandle = ?', array($wpCategoryHandle));
        $db->executeQuery('insert into WorkflowProgress (wfID, wrID, wpDateAdded, wpCategoryID) values (?, ?, ?, ?)', array(
            $wf->getWorkflowID(), $wr->getWorkflowRequestID(), $wpDateAdded, $wpCategoryID,
        ));
        $wp = self::getByID($db->lastInsertId());
        $wp->addWorkflowProgressHistoryObject($wr);


        if (!($wf instanceof EmptyWorkflow)) {
            $application = \Core::getFacadeApplication();
            $type = $application->make('manager/notification/types')->driver('workflow_progress');
            $notifier = $type->getNotifier();
            $subscription = $type->getSubscription($wp);
            $notified = $notifier->getUsersToNotify($subscription, $wp);
            $notification = $type->createNotification($wp);
            $notifier->notify($notified, $notification);
        }

        return $wp;
    }

    public function delete()
    {
        $db = Database::connection();
        $wr = $this->getWorkflowRequestObject();
        $db->executeQuery('delete from WorkflowProgress where wpID = ?', array($this->wpID));
        // now we clean up any WorkflowRequests that aren't in use any longer
        $cnt = $db->fetchColumn('select count(wpID) from WorkflowProgress where wrID = ?', array($this->wrID));
        if ($cnt == 0) {
            $wr->delete();

            if (!($this->getWorkflowObject() instanceof EmptyWorkflow)) {
                // Remove the associated notification
                $em = $db->getEntityManager();
                $r = $em->getRepository('Concrete\Core\Entity\Notification\WorkflowProgressNotification');
                $notification = $r->findOneBy(array('wpID' => $this->getWorkflowProgressID()));
                if (is_object($notification)) {
                    // The refresh is needed because the relation is lazy loaded
                    $em->refresh($notification);
                    $em->remove($notification);
                    $em->flush();
                }
            }
        }
    }

    public static function getByID($wpID)
    {
        $db = Database::connection();
        $r = $db->fetchAssoc('select WorkflowProgress.*, WorkflowProgressCategories.wpCategoryHandle, WorkflowProgressCategories.pkgID from WorkflowProgress inner join WorkflowProgressCategories on WorkflowProgress.wpCategoryID = WorkflowProgressCategories.wpCategoryID where wpID  = ?', array($wpID));
        if (!is_array($r) || (!$r['wpID'])) {
            return false;
        }
        $class = '\\Core\\Workflow\\Progress\\' . Core::make('helper/text')->camelcase($r['wpCategoryHandle']) . 'Progress';

        $pkgHandle = $r['pkgID'] ? PackageList::getHandle($r['pkgID']) : null;
        $class = core_class($class, $pkgHandle);
        $wp = Core::make($class);
        $wp->setPropertiesFromArray($r);
        $wp->loadDetails();

        return $wp;
    }

    public static function getRequestedTask()
    {
        $task = '';
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'action_') > -1) {
                return substr($key, 7);
            }
        }
    }

    /**
     * The function that is automatically run when a workflowprogress object is started.
     */
    public function start()
    {
        $wf = $this->getWorkflowObject();
        if (is_object($wf)) {
            $r = $wf->start($this);
            $this->updateOnAction($wf);
        }

        return $r;
    }

    public function updateOnAction(Workflow $wf)
    {
        $db = Database::connection();
        $num = $wf->getWorkflowProgressCurrentStatusNum($this);
        $time = Core::make('helper/date')->getOverridableNow();
        $db->executeQuery('update WorkflowProgress set wpDateLastAction = ?, wpCurrentStatus = ? where wpID = ?', array($time, $num, $this->wpID));
    }

    /**
     * Attempts to run a workflow task on the bound WorkflowRequest object first, then if that doesn't exist, attempts to run
     * it on the current WorkflowProgress object.
     *
     * @return WorkflowProgressResponse
     */
    public function runTask($task, $args = array())
    {
        $wf = $this->getWorkflowObject();
        if (in_array($task, $wf->getAllowedTasks())) {
            $wpr = call_user_func_array(array($wf, $task), array($this, $args));
            $this->updateOnAction($wf);
        }
        if (!($wpr instanceof Response)) {
            $wpr = new Response();
        }

        $event = new GenericEvent();
        $event->setArgument('response', $wpr);

        Events::dispatch('workflow_progressed', $event);

        return $wpr;
    }

    public function getWorkflowProgressActions($additionalActions = true)
    {
        $w = $this->getWorkflowObject();
        $req = $this->getWorkflowRequestObject();
        if ($additionalActions) {
            $actions = $req->getWorkflowRequestAdditionalActions($this);
        } else {
            $actions = array();
        }
        $actions = array_merge($actions, $w->getWorkflowProgressActions($this));

        return $actions;
    }

    abstract public function getWorkflowProgressFormAction();
    abstract public function loadDetails();

    public function getWorkflowProgressHistoryObjectByID($wphID)
    {
        $class = '\\Concrete\\Core\\Workflow\\Progress\\' . camelcase($this->getWorkflowProgressCategoryHandle()) . 'History';
        $db = Database::connection();
        $row = $db->fetchAssoc('select * from WorkflowProgressHistory where wphID = ?', array($wphID));
        if (is_array($row) && ($row['wphID'])) {
            $obj = new $class();
            $obj->setPropertiesFromArray($row);
            $obj->object = @unserialize($row['object']);

            return $obj;
        }
    }

    public function addWorkflowProgressHistoryObject($obj)
    {
        $db = Database::connection();
        $db->executeQuery('insert into WorkflowProgressHistory (wpID, object) values (?, ?)', array($this->wpID, serialize($obj)));
    }

    public function markCompleted()
    {
        $wf = $this->getWorkflowObject();

        $db = Database::connection();
        $db->executeQuery('update WorkflowProgress set wpIsCompleted = 1 where wpID = ?', array($this->wpID));

        if (!($wf instanceof EmptyWorkflow)) {
            $application = \Core::getFacadeApplication();
            $type = $application->make('manager/notification/types')->driver('workflow_progress');
            $type->clearNotification($this);
        }
    }

    abstract public function getPendingWorkflowProgressList();
}

<?php
namespace Concrete\Core\Workflow;

use \Concrete\Core\Foundation\Object;
use Concrete\Core\Package\Package;
use \Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Loader;
use Core;
use Concrete\Core\Workflow\Request\Request as WorkflowRequest;

/**
 * \@package Workflow
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
abstract class Workflow extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    protected $wfID = 0;
    protected $allowedTasks = array('cancel', 'approve');
    protected $restrictedToPermissionKeyHandles = array();

    public function getAllowedTasks()
    {
        return $this->allowedTasks;
    }

    public function getWorkflowID()
    {
        return $this->wfID;
    }

    public function getWorkflowName()
    {
        return $this->wfName;
    }

    /**
     * Returns the display name for this workflow (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getWorkflowDisplayName($format = 'html')
    {
        $value = tc('WorkflowName', $this->getWorkflowName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getWorkflowTypeObject()
    {
        return Type::getByID($this->wftID);
    }

    public function getRestrictedToPermissionKeyHandles()
    {
        return $this->restrictedToPermissionKeyHandles;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\Workflow';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\WorkflowAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'basic_workflow';
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getWorkflowID();
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from Workflows where wfID = ?', array($this->wfID));

        foreach (
            $db->GetArray(
                'select wpID from WorkflowProgress where wfID = ?',
                array($this->wfID)
            )
            as $row
        ) {
            $wfp = WorkflowProgress::getByID($row['wpID']);
            if ($wfp) {
                $wfp->delete();
            }
        }
    }

    // by default the basic workflow just passes the status num from the request
    // we do this so that we can order things by most important, etc...
    public function getWorkflowProgressCurrentStatusNum(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();
        if (is_object($req)) {
            return $req->getWorkflowRequestStatusNum();
        }
    }

    public static function getList()
    {
        $workflows = array();
        $db = Loader::db();
        $r = $db->Execute("select wfID from Workflows order by wfName asc");
        while ($row = $r->FetchRow()) {
            $wf = static::getByID($row['wfID']);
            if (is_object($wf)) {
                $workflows[] = $wf;
            }
        }

        return $workflows;
    }

    public static function getListByPackage(\Concrete\Core\Entity\Package $pkg)
    {
        $workflows = array();
        $db = Loader::db();
        $r = $db->Execute("select wfID from Workflows where pkgID = ? order by wfName asc", [$pkg->getPackageID()]);
        while ($row = $r->FetchRow()) {
            $wf = static::getByID($row['wfID']);
            if (is_object($wf)) {
                $workflows[] = $wf;
            }
        }

        return $workflows;
    }


    public static function add(Type $wt, $name, \Concrete\Core\Entity\Package $pkg = null)
    {
        $db = Loader::db();
        $wfID = $db->getOne('SELECT wfID FROM Workflows WHERE wfName=?', array($name));
        if (!$wfID) {
            $pkgID = 0;
            if (is_object($pkg)) {
                $pkgID = $pkg->getPackageID();
            }

            $db->Execute('insert into Workflows (wftID, wfName, pkgID) values (?, ?, ?)', array($wt->getWorkflowTypeID(), $name, $pkgID));
            $wfID = $db->Insert_ID();
        }

        return self::getByID($wfID);
    }

    protected function load($wfID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select Workflows.* from Workflows where Workflows.wfID = ?', array($wfID));
        $this->setPropertiesFromArray($r);
    }

    public static function getByID($wfID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select WorkflowTypes.wftHandle, WorkflowTypes.pkgID from Workflows inner join WorkflowTypes on Workflows.wftID = WorkflowTypes.wftID where Workflows.wfID = ?',
            array($wfID));
        if ($r['wftHandle']) {
            $class = '\\Core\\Workflow\\' . Loader::helper('text')->camelcase($r['wftHandle']) . 'Workflow';
            if ($r['pkgID']) {
                $pkg = Package::getByID($r['pkgID']);
                $prefix = $pkg->getPackageHandle();
            }
            $class = core_class($class, $prefix);
            $obj = Core::make($class);

            $obj->load($wfID);
            if ($obj->getWorkflowID() > 0) {
                $obj->loadDetails();

                return $obj;
            }
        }
    }

    public static function getByName($wfName)
    {
        $db = Loader::db();
        $wfID = $db->GetOne('select wfID from Workflows where wfName = ?', array($wfName));
        if ($wfID) {
            return static::getByID($wfID);
        }
    }

    public function getWorkflowToolsURL($task)
    {
        $type = $this->getWorkflowTypeObject();
        $uh = Loader::helper('concrete/urls');
        $url = $uh->getToolsURL('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle());
        $url .= '?wfID=' . $this->getWorkflowID() . '&task=' . $task . '&' . Loader::helper('validation/token')->getParameter($task);

        return $url;
    }

    public function updateName($wfName)
    {
        $db = Loader::db();
        $db->Execute('update Workflows set wfName = ? where wfID = ?', array($wfName, $this->wfID));
    }

    abstract public function start(WorkflowProgress $wp);

    abstract public function canApproveWorkflow();

    abstract public function getWorkflowProgressApprovalUsers(WorkflowProgress $wp);

    abstract public function getWorkflowProgressActions(WorkflowProgress $wp);

    abstract public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp);

    abstract public function getWorkflowProgressCurrentComment(WorkflowProgress $wp);

    abstract public function getWorkflowProgressStatusDescription(WorkflowProgress $wp);

    abstract public function canApproveWorkflowProgressObject(WorkflowProgress $wp);

    abstract public function updateDetails($vars);

    abstract public function loadDetails();

    public function getPermissionAccessObject()
    {
        return false;
    }

    public function validateTrigger(WorkflowRequest $req)
    {
        // Check if the current workflow request is not already deleted
        $wr = $req::getByID($req->getWorkflowRequestID());
        if (is_object($wr)) {
            return true;
        }

        return false;
    }
}

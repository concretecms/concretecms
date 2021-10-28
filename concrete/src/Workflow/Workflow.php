<?php

namespace Concrete\Core\Workflow;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Package;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Request\Request as WorkflowRequest;
use Punic\Comparer;

abstract class Workflow extends ConcreteObject implements ObjectInterface
{
    /**
     * The workflow ID.
     *
     * @var int
     */
    protected $wfID = 0;

    /**
     * The list of allowed tasks.
     *
     * @var string[]
     */
    protected $allowedTasks = ['cancel', 'approve'];

    /**
     * The list of permission key handles that this workflow can be attached to.
     *
     * @var string[]
     */
    protected $restrictedToPermissionKeyHandles = [];

    /**
     * Get the workflow ID.
     *
     * @return int
     */
    public function getWorkflowID()
    {
        return $this->wfID;
    }

    /**
     * Get the workflow (English) name.
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->wfName;
    }

    /**
     * Get the display name for this workflow (localized and escaped accordingly to $format).
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

    /**
     * Get the list of allowed tasks.
     *
     * @return string[]
     */
    public function getAllowedTasks()
    {
        return $this->allowedTasks;
    }

    /**
     * Get the workflow type associated to this workflow.
     *
     * @return \Concrete\Core\Workflow\Type|null
     */
    public function getWorkflowTypeObject()
    {
        return empty($this->wftID) ? null : Type::getByID($this->wftID);
    }

    /**
     * Get the list of permission key handles that this workflow can be attached to.
     *
     * @var string[]
     */
    public function getRestrictedToPermissionKeyHandles()
    {
        return $this->restrictedToPermissionKeyHandles;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\Workflow';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\WorkflowAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'basic_workflow';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->getWorkflowID();
    }

    /**
     * Delete this workflow and all its associated progresses.
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rows = $db->fetchAll('select wpID from WorkflowProgress where wfID = ?', [$this->getWorkflowID()]);
        foreach ($rows as $row) {
            $wfp = WorkflowProgress::getByID($row['wpID']);
            if ($wfp) {
                $wfp->delete();
            }
        }
        $db->delete('Workflows', ['wfID' => $this->getWorkflowID()]);
    }

    /**
     * By default the basic workflow just passes the status num from the request
     * we do this so that we can order things by most important, etc...
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return int|null
     */
    public function getWorkflowProgressCurrentStatusNum(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();
        if (is_object($req)) {
            return $req->getWorkflowRequestStatusNum();
        }
    }

    /**
     * Get the list of installed workflows, sorted by the workflow display name.
     *
     * @return \Concrete\Core\Workflow\Workflow[]
     */
    public static function getList()
    {
        $workflows = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rows = $db->fetchAll('select wfID from Workflows');
        foreach ($rows as $row) {
            $wf = static::getByID($row['wfID']);
            if ($wf) {
                $workflows[] = $wf;
            }
        }
        $cmp = new Comparer();
        usort($workflows, function (Workflow $a, Workflow $b) use ($cmp) {
            return $cmp->compare($a->getWorkflowDisplayName('text'), $b->getWorkflowDisplayName('text'));
        });

        return $workflows;
    }

    /**
     * Get the list of workflows installed by a package, sorted by the workflow display name.
     *
     * @param \Concrete\Core\Entity\Package $pkg
     *
     * @return \Concrete\Core\Workflow\Workflow[]
     */
    public static function getListByPackage(Package $pkg)
    {
        $workflows = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rows = $db->fetchAll('select wfID from Workflows where pkgID = ?', [$pkg->getPackageID()]);
        foreach ($rows as $row) {
            $wf = static::getByID($row['wfID']);
            if ($wf) {
                $workflows[] = $wf;
            }
        }
        $cmp = new Comparer();
        usort($workflows, function (Workflow $a, Workflow $b) use ($cmp) {
            return $cmp->compare($a->getWorkflowDisplayName('text'), $b->getWorkflowDisplayName('text'));
        });

        return $workflows;
    }

    /**
     * Create a new workflow.
     *
     * @param \Concrete\Core\Workflow\Type $wt The workflow type
     * @param string $name the (English) name of the workflow
     * @param \Concrete\Core\Entity\Package|null $pkg the package that's creating the new workflow
     *
     * @return \Concrete\Core\Workflow\Workflow
     */
    public static function add(Type $wt, $name, Package $pkg = null)
    {
        $wf = static::getByName($name);
        if ($wf !== null) {
            return $wf;
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->insert(
            'Workflows',
            [
                'wftID' => $wt->getWorkflowTypeID(),
                'wfName' => (string) $name,
                'pkgID' => $pkg === null ? $wt->getPackageID() : $pkg->getPackageID(),
            ]
        );
        $wfID = $db->lastInsertId();

        return self::getByID($wfID);
    }

    /**
     * Get a workflow given its ID.
     *
     * @param int $wfID the ID of the workflow
     *
     * @return \Concrete\Core\Workflow\Workflow|null
     */
    public static function getByID($wfID)
    {
        $wfID = (int) $wfID;
        if ($wfID === 0) {
            return null;
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $r = $db->fetchAssoc(<<<'EOT'
select
    WorkflowTypes.wftHandle,
    WorkflowTypes.pkgID
from
    Workflows
    inner join WorkflowTypes on Workflows.wftID = WorkflowTypes.wftID
where
    Workflows.wfID = ?
EOT
            ,
            [$wfID]
        );
        if (!$r) {
            return null;
        }
        $class = '\\Core\\Workflow\\' . camelcase($r['wftHandle']) . 'Workflow';
        if ($r['pkgID']) {
            $pkg = $app->make(PackageService::class)->getByID($r['pkgID']);
            $prefix = $pkg->getPackageHandle();
        } else {
            $prefix = null;
        }
        $class = core_class($class, $prefix);
        $obj = $app->make($class);
        /** @var \Concrete\Core\Workflow\Workflow $obj */
        $obj->load($wfID);
        if (!$obj->getWorkflowID()) {
            return null;
        }
        $obj->loadDetails();

        return $obj;
    }

    /**
     * Get a workflow given its (English) name.
     *
     * @param string $wfName
     *
     * @return \Concrete\Core\Workflow\Workflow|null
     */
    public static function getByName($wfName)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $wfID = $db->fetchColumn('SELECT wfID FROM Workflows WHERE wfName = ?', [(string) $wfName]);

        return $wfID ? static::getByID($wfID) : null;
    }

    /**
     * Change the (English) name of this workflow.
     *
     * @param string $wfName
     */
    public function updateName($wfName)
    {
        $wfName = (string) $wfName;
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->update(
            'Workflows',
            [
                'wfName' => $wfName,
            ],
            [
                'wfID' => $this->getWorkflowID(),
            ]
        );
        $this->wfName = $wfName;
    }

    /**
     * Start the workflow.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return \Concrete\Core\Workflow\Progress\Response|\Concrete\Core\Workflow\Progress\SkippedResponse|null
     */
    abstract public function start(WorkflowProgress $wp);

    /**
     * Check if the currently logged-in user can approve this workflow.
     *
     * @return bool
     */
    abstract public function canApproveWorkflow();

    /**
     * Get the list of users that can approve an operation.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return \Concrete\Core\User\UserInfo[]
     */
    abstract public function getWorkflowProgressApprovalUsers(WorkflowProgress $wp);

    /**
     * Get the list of actions that can be performed against an operation.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return \Concrete\Core\Workflow\Progress\Action\Action[]
     */
    abstract public function getWorkflowProgressActions(WorkflowProgress $wp);

    /**
     * Get the comments about an operation.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return string|false|null
     */
    abstract public function getWorkflowProgressCurrentComment(WorkflowProgress $wp);

    /**
     * Get the description of the status of an operation.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return string
     */
    abstract public function getWorkflowProgressStatusDescription(WorkflowProgress $wp);

    /**
     * Check if the currently logged-in user can approve an operation.
     *
     * @param \Concrete\Core\Workflow\Progress\Progress $wp
     *
     * @return bool
     */
    abstract public function canApproveWorkflowProgressObject(WorkflowProgress $wp);

    /**
     * Update the workflow details with data (usually received via POST).
     *
     * @param array $vars
     */
    abstract public function updateDetails($vars);

    /**
     * Load the details of this workflow (usually called right after this instance has been created).
     */
    abstract public function loadDetails();

    /**
     * @return bool
     */
    public function getPermissionAccessObject()
    {
        return false;
    }

    /**
     * Check if a workflow request is valid.
     *
     * @param \Concrete\Core\Workflow\Request\Request $req
     *
     * @return bool
     */
    public function validateTrigger(WorkflowRequest $req)
    {
        // Check if the current workflow request is not already deleted
        $wr = $req::getByID($req->getWorkflowRequestID());

        return is_object($wr);
    }

    /**
     * Load the workflow data from the database row.
     *
     * @param int $wfID
     */
    protected function load($wfID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $r = $db->fetchAssoc('select * from Workflows where wfID = ?', [(int) $wfID]);
        $r['wfID'] = (int) $r['wfID'];
        $r['wftID'] = (int) $r['wftID'];
        $r['pkgID'] = (int) $r['pkgID'];
        $this->setPropertiesFromArray($r);
    }
}

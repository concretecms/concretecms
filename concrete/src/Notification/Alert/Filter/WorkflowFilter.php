<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Alert\AlertList;
use Concrete\Core\Workflow\Workflow;

class WorkflowFilter implements FilterInterface
{

    /**
     * @var Workflow
     */
    protected $workflow;

    /**
     * Filter constructor.
     * @param $workflow
     */
    public function __construct(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function getName()
    {
        return t('Workflow: %s', $this->workflow->getWorkflowName());
    }

    public function getKey()
    {
        return sprintf('workflow_%s', $this->workflow->getWorkflowID());
    }

    public function filterAlertList(AlertList $list)
    {
        $list->getQueryObject()->andWhere('wp.wfID = :wfID');
        $list->getQueryObject()->setParameter('wfID', $this->workflow->getWorkflowID());
    }
}

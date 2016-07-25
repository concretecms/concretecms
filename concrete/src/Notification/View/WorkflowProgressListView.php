<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Entity\Notification\UserSignupNotification;
use Concrete\Core\Entity\Notification\WorkflowProgressNotification;
use Concrete\Core\Notification\View\Menu\WorkflowProgressListViewMenu;
use Concrete\Core\Workflow\Progress\Progress;
use HtmlObject\Element;

class WorkflowProgressListView extends StandardListView
{

    protected $progress;

    protected $workflow;

    protected $request;

    protected $actions = array();

    /**
     * WorkflowProgressListView constructor.
     * @param Notification WorkflowProgressNotification
     */
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
        $this->progress = $this->notification->getWorkflowProgressObject();
        $this->workflow = $this->progress->getWorkflowObject();
        $this->request = $this->progress->getWorkflowRequestObject();
        $this->actions = $this->progress->getWorkflowProgressActions();
    }

    /**
     * @var UserSignupNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Workflow Progress');
    }

    public function getIconClass()
    {
        // Not used, since renderIcon() is overridden
        return false;
    }

    public function renderIcon()
    {
        return $this->request->getRequestIconElement();
    }


    public function getInitiatorUserObject()
    {
        return $this->request->getRequesterUserObject();
    }

    public function getActionDescription()
    {
        $description = $this->request->getWorkflowRequestDescriptionObject();
        return $description->getDescription();
    }

    public function getInitiatorComment()
    {
        return $this->request->getRequesterComment();
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Requested By '));
    }

    public function getFormAction()
    {
        return $this->progress->getWorkflowProgressFormAction();
    }

    public function getMenu()
    {
        $menu = new WorkflowProgressListViewMenu();
        foreach($this->actions as $action) {
            if ($action->getWorkflowProgressActionURL() != '') {
                $parameters = array_merge(array('class' => $action->getWorkflowProgressActionStyleClass()), $action->getWorkflowProgressActionExtraButtonParameters());
                $item = new LinkItem(
                    $action->getWorkflowProgressActionURL() . '&source=dashboard',
                    $action->getWorkflowProgressActionLabel(),
                    $parameters
                );
            } else {
                $parameters = array_merge(array(
                    'data-workflow-task' => $action->getWorkflowProgressActionTask(),
                ), $action->getWorkflowProgressActionExtraButtonParameters());

                $item = new LinkItem(
                    '#',
                    $action->getWorkflowProgressActionLabel(),
                    $parameters
                );
            }
            $menu->addItem($item);
        }
        return $menu;
    }

}
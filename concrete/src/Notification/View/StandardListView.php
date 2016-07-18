<?php
namespace Concrete\Core\Notification\View;

use Concrete\Controller\Element\Notification\ListDetails;
use Concrete\Controller\Element\Notification\Menu;
use Concrete\Core\Entity\Notification\Notification;
use HtmlObject\Element;

abstract class StandardListView implements StandardListViewInterface
{

    protected $notification;

    public function getNotificationObject()
    {
        return $this->notification;
    }

    public function renderIcon()
    {
        $element = new Element('i');
        $element->addClass($this->getIconClass());
        return $element;
    }

    public function renderDetails()
    {
        $details = new ListDetails();
        return $details->render();
    }

    public function renderMenu()
    {
        $menu = new Menu();
        return $menu->render();
    }

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getShortDescription()
    {
        return '';
    }

    public function getRequesterUserObject()
    {
        return null;
    }

    public function getRequesterComment()
    {
        return null;
    }

    public function getActions()
    {
        return array();
    }


}

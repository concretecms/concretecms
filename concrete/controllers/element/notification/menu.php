<?php
namespace Concrete\Controller\Element\Notification;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\View\ListViewInterface;

class Menu extends ElementController
{

    public function getElement()
    {
        return 'notification/menu';
    }

    public function view()
    {
    }
}

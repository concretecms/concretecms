<?php
namespace Concrete\Controller\Element\Notification;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\View\ListViewInterface;

class ListDetails extends ElementController
{

    public function getElement()
    {
        return 'notification/list/details';
    }

    public function view()
    {
        $this->set('listView', $this->listView);
    }
}

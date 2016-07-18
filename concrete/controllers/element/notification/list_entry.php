<?php
namespace Concrete\Controller\Element\Notification;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\View\ListViewInterface;

class ListEntry extends ElementController
{

    protected $listView;

    public function __construct(ListViewInterface $listView)
    {
        parent::__construct();
        $this->listView = $listView;
    }

    public function getElement()
    {
        return 'notification/list/entry';
    }

    public function view()
    {
        $this->set('listView', $this->listView);
    }
}

<?php
namespace Concrete\Controller\Element\Notification;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\View\ListViewInterface;
use Concrete\Core\Notification\View\StandardListViewInterface;

/**
 * @since 8.0.0
 */
class ListDetails extends ElementController
{

    protected $listView;

    public function __construct(StandardListViewInterface $listView)
    {
        $this->listView = $listView;
        parent::__construct();
    }

    public function getElement()
    {
        return 'notification/list/details';
    }

    public function view()
    {
        $this->set('listView', $this->listView);
    }
}

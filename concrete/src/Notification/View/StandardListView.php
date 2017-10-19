<?php
namespace Concrete\Core\Notification\View;

use Concrete\Controller\Element\Notification\ListDetails;
use Concrete\Controller\Element\Notification\Menu;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Support\Facade\Facade;
use HtmlObject\Element;
use HtmlObject\Link;

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
        $details = new ListDetails($this);
        return $details->render();
    }

    public function renderMenu()
    {
        $menu = new Menu($this);
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

    public function getInitiatorUserObject()
    {
        return null;
    }

    public function getInitiatorComment()
    {
        return null;
    }

    public function getMenu()
    {
        return false;
    }

    public function getFormAction()
    {
        return false;
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Requested By '));
    }

    public function renderInitiatorCommentDescription()
    {
        $comment = $this->getInitiatorComment();
        if ($comment) {
            $commentElement = new Element('div', $comment, array('class' => 'ccm-block-desktop-waiting-for-me-author-comment'));
            return $commentElement;
        }
    }

    public function renderInitiatorActionDescription()
    {

        $user = $this->getInitiatorUserObject();
        $element = $this->getRequestedByElement();
        $date = $this->notification->getNotificationDate();
        $app = Facade::getFacadeApplication();
        $service = $app->make('date');
        if (is_object($user)) {
            $inner = new Element('span', null, array('class' => 'ccm-block-desktop-waiting-for-me-author'));

            $link = new Link('#', $user->getUserDisplayName());

            $element->appendChild($inner);
            $inner->appendChild($link);

            $dateElement = new Element('span', tc('date', ' on %s', $service->formatDateTime($date, false, false, $this->notification->getNotificationDateTimeZone())), array('class' => 'ccm-block-desktop-waiting-for-me-date'));
            $element->appendChild($dateElement);

            return $element;
        } else {
            // No requested by element
            $element = new Element('span', $service->formatDateTime($date, false, false, $this->notification->getNotificationDateTimeZone()), array('class' => 'ccm-block-desktop-waiting-for-me-date'));
            return $element;
        }


    }

}

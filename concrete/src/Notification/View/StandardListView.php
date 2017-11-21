<?php
namespace Concrete\Core\Notification\View;

use Concrete\Controller\Element\Notification\ListDetails;
use Concrete\Controller\Element\Notification\Menu;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\Url;
use Concrete\Core\User\UserInfo;
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

    /**
     * @return UserInfo
     */
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

    protected function getLinkToUser(UserInfo $user)
    {
        $app = Facade::getFacadeApplication();
        return $app->make('url/manager')->resolve([$user]);
    }

    public function getDateString()
    {
        $app = Facade::getFacadeApplication();
        $date = $this->notification->getNotificationDate();
        $service = $app->make('date');
        $user = $this->getInitiatorUserObject();
        $timezone = null;
        if ($user) {
            $timezone = $user->getUserTimezone();
        }
        if (!$timezone) {
            $timezone = $this->notification->getNotificationDateTimeZone();
        }
        return $service->formatDateTime($date, false, false, $timezone);
    }

    public function renderInitiatorActionDescription()
    {

        $user = $this->getInitiatorUserObject();
        $element = $this->getRequestedByElement();
        if (is_object($user)) {
            $inner = new Element('span', null, array('class' => 'ccm-block-desktop-waiting-for-me-author'));

            $link = new Link($this->getLinkToUser($user), $user->getUserDisplayName());

            $element->appendChild($inner);
            $inner->appendChild($link);

            $dateElement = new Element('span', tc('date', ' on %s', $this->getDateString()), array('class' => 'ccm-block-desktop-waiting-for-me-date'));
            $element->appendChild($dateElement);

            return $element;
        } else {
            // No requested by element
            $element = new Element('span', $this->getDateString(), array('class' => 'ccm-block-desktop-waiting-for-me-date'));
            return $element;
        }


    }

}

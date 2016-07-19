<?php

class NotificationTest extends \PHPUnit_Framework_TestCase
{

    protected function getUserSignup()
    {
        $user = new \Concrete\Core\Entity\User\User();
        $user->setUserDateAdded(new \DateTime('2008-12-01 05:00:00'));
        $user->setUserName('andrew');
        $signup = new \Concrete\Core\Entity\User\UserSignup($user);
        return $signup;
    }

    public function testCreateNotification()
    {
        $signup = $this->getUserSignup();
        $driver = new \Concrete\Core\Notification\Factory\UserSignupFactory();
        $notification = $driver->createNotification($signup);

        $this->assertInstanceOf('Concrete\Core\Notification\Factory\FactoryInterface', $driver);
        $this->assertInstanceOf('Concrete\Core\Entity\Notification\UserSignupNotification', $notification);
        $this->assertEquals($notification->getNotificationDate(), $signup->getNotificationDate());
    }


    public function testFormatNotificationView()
    {
        $signup = $this->getUserSignup();
        $driver = new \Concrete\Core\Notification\Factory\UserSignupFactory();
        $notification = $driver->createNotification($signup);

        $view = $notification->getListView();

        $this->assertInstanceOf('Concrete\Core\Notification\View\UserSignupListView', $view);
        $this->assertInstanceOf('Concrete\Core\Notification\View\StandardListViewInterface', $view);
        $this->assertInstanceOf('Concrete\Core\Notification\View\ListViewInterface', $view);

        $icon = (string) $view->renderIcon();
        $this->assertEquals('<i class="fa fa-user"></i>', $icon);


    }

    public function testNotificationSubscribers()
    {
        $message = new \Concrete\Core\Conversation\Message\Message();
        $message->cnvMessageID = 10;
        $message->cnvMessageDateCreated = '2010-12-11 00:00:00';
        $driver = new \Concrete\Core\Notification\Factory\NewConversationMessageFactory();
        $newMessage = new \Concrete\Core\Conversation\Message\NewMessage($message);

        $notification = $driver->createNotification($newMessage);


    }
}
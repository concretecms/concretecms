<?php

namespace Concrete\Tests\Notification;

use PHPUnit_Framework_TestCase;

class NotificationTest extends PHPUnit_Framework_TestCase
{
    public function testCreateNotification()
    {
        $signup = $this->getUserSignup();
        $driver = new \Concrete\Core\Notification\Type\UserSignupType(\Database::get()->getEntityManager());
        $notification = $driver->createNotification($signup);

        $this->assertInstanceOf('Concrete\Core\Notification\Type\TypeInterface', $driver);
        $this->assertInstanceOf('Concrete\Core\Entity\Notification\UserSignupNotification', $notification);
        $this->assertEquals($notification->getNotificationDate(), $signup->getNotificationDate());
    }

    public function testFormatNotificationView()
    {
        $signup = $this->getUserSignup();
        $driver = new \Concrete\Core\Notification\Type\UserSignupType(\Database::get()->getEntityManager());
        $notification = $driver->createNotification($signup);

        $view = $notification->getListView();

        $this->assertInstanceOf('Concrete\Core\Notification\View\UserSignupListView', $view);
        $this->assertInstanceOf('Concrete\Core\Notification\View\StandardListViewInterface', $view);
        $this->assertInstanceOf('Concrete\Core\Notification\View\ListViewInterface', $view);

        $icon = (string) $view->renderIcon();
        $this->assertEquals('<i class="fa fa-user"></i>', $icon);
    }

    public function testNotificationTypeManager()
    {
        $manager = \Core::make('manager/notification/types');
        $this->assertInstanceOf('Concrete\Core\Notification\Type\Manager', $manager);

        $manager = \Core::make('manager/notification/subscriptions');
        $this->assertInstanceOf('Concrete\Core\Notification\Subscription\Manager', $manager);

        /**
         * @var \Concrete\Core\Notification\Subscription\Manager
         */
        $subscriptions = $manager->getSubscriptions();
        $this->assertEquals(7, count($subscriptions));
    }

    protected function getUserSignup()
    {
        $user = new \Concrete\Core\Entity\User\User();
        $user->setUserDateAdded(new \DateTime('2008-12-01 05:00:00'));
        $user->setUserName('andrew');
        $signup = new \Concrete\Core\Entity\User\UserSignup($user);

        return $signup;
    }
}

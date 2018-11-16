<?php

namespace Concrete\Tests\Notification\View;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Notification\UserDeactivatedNotification;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Notification\View\UserDeactivatedListView;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;

class UserDeactivatedListViewTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    public function testManualViewDetails()
    {
        $deactivatedUser = M::mock(User::class);
        $deactivatedUser->shouldReceive('getUserID')->once()->andReturn(55);
        $deactivatedUser->shouldReceive('getUserName')->once()->andReturn('Cletus');

        $actorUser = M::mock(User::class);
        $actorUser->shouldReceive('getUserID')->once()->andReturn(66);
        $actorUser->shouldReceive('getUserName')->once()->andReturn('Almyra');

        $em = M::mock(EntityManagerInterface::class);
        $em->shouldReceive('find')->once()->withArgs([User::class, 55])->andReturn($deactivatedUser);
        $em->shouldReceive('find')->once()->withArgs([User::class, 66])->andReturn($actorUser);

        $urlResolver = M::mock(ResolverManagerInterface::class);
        $urlResolver->shouldReceive('resolve')->twice()->andReturn('');

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->once()->withArgs([EntityManagerInterface::class])->andReturn($em);
        $app->shouldReceive('make')->once()->withArgs([ResolverManagerInterface::class])->andReturn($urlResolver);

        $notification = M::mock(UserDeactivatedNotification::class);
        $notification->shouldReceive('getUserID')->atLeast()->once()->andReturn(55);
        $notification->shouldReceive('getActorID')->atLeast()->once()->andReturn(66);

        $view = new UserDeactivatedListView($notification);
        $view->setApplication($app);

        $this->assertEquals('fa fa-user-times', $view->getIconClass());
        $this->assertEquals('User Deactivated', $view->getTitle());
        $this->assertStringMatchesFormat('%s has been manually deactivated by %s', $view->getActionDescription());
    }

    public function testAutomaticViewDetails()
    {
        $deactivatedUser = M::mock(User::class);
        $deactivatedUser->shouldReceive('getUserID')->once()->andReturn(55);
        $deactivatedUser->shouldReceive('getUserName')->once()->andReturn('Cletus');

        $em = M::mock(EntityManagerInterface::class);
        $em->shouldReceive('find')->once()->withArgs([User::class, 55])->andReturn($deactivatedUser);

        $urlResolver = M::mock(ResolverManagerInterface::class);
        $urlResolver->shouldReceive('resolve')->once()->andReturn('');

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->once()->withArgs([EntityManagerInterface::class])->andReturn($em);
        $app->shouldReceive('make')->once()->withArgs([ResolverManagerInterface::class])->andReturn($urlResolver);

        $notification = M::mock(UserDeactivatedNotification::class);
        $notification->shouldReceive('getUserID')->atLeast()->once()->andReturn(55);
        $notification->shouldReceive('getActorID')->atLeast()->once()->andReturn(null);

        $view = new UserDeactivatedListView($notification);
        $view->setApplication($app);

        $this->assertStringMatchesFormat('%s has been automatically deactivated.', $view->getActionDescription());
    }
}

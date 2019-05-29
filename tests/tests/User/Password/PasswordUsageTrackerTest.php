<?php

namespace Concrete\Tests\User\Password;

use Concrete\Core\Entity\User\User as EntityUser;
use Concrete\Core\Entity\Validator\UsedString;
use Concrete\Core\User\Password\PasswordUsageTracker;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;
use ReflectionMethod;

class PasswordUsageTrackerTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    public function testTrackUse()
    {
        $repository = M::mock(ObjectRepository::class);
        $repository->shouldReceive('findBy')->with(['subject' => 1337], ['id' => 'desc'])->andReturn([]);

        $entityManager = M::mock(EntityManagerInterface::class);
        $entityManager->shouldReceive('transactional')->with(\Closure::class)->once()
            ->andReturnUsing(function($closure) use ($entityManager) {
                $closure($entityManager);
            });

        $entityManager->shouldReceive('getRepository')->with(UsedString::class)->once()->andReturn($repository);
        $entityManager->shouldReceive('persist')->with(UsedString::class)->once()->andReturnUsing(function(UsedString $string) {
            $this->assertTrue(password_verify('foo', $string->getUsedString()));
            $this->assertEquals(1337, $string->getSubject());
        });

        $tracker = new PasswordUsageTracker($entityManager, 1);
        $this->assertTrue($tracker->trackUse('foo', 1337));
    }

    public function testDoesNothingWithInvalidSubject()
    {
        $entityManager = M::mock(EntityManagerInterface::class);
        $tracker = new PasswordUsageTracker($entityManager, 1);

        $this->assertFalse($tracker->trackUse('foo', 0));
    }

    public function testUserNegotiation()
    {
        $method = new ReflectionMethod(PasswordUsageTracker::class, 'resolveUserID');
        $method->setAccessible(true);

        $entityManager = M::mock(EntityManagerInterface::class);
        $tracker = new PasswordUsageTracker($entityManager, 1);

        $id = 1337;
        $user = M::mock(User::class)->shouldReceive(['getUserID' => $id])->getMock();
        $userInfo = M::mock(UserInfo::class)->shouldReceive(['getUserID' => $id])->getMock();
        $userEntity = M::mock(EntityUser::class)->shouldReceive(['getUserID' => $id])->getMock();

        $this->assertEquals($id, $method->invoke($tracker, 1337));
        $this->assertEquals($id, $method->invoke($tracker, $user));
        $this->assertEquals($id, $method->invoke($tracker, $userInfo));
        $this->assertEquals($id, $method->invoke($tracker, $userEntity));
    }

    public function testPruning()
    {
        $repository = M::mock(ObjectRepository::class);
        $repository->shouldReceive('findBy')->with(['subject' => 1337], ['id' => 'desc'])->andReturn([5, 4, 3, 2, 1]);

        $method = new ReflectionMethod(PasswordUsageTracker::class, 'pruneUses');
        $method->setAccessible(true);

        $entityManager = M::mock(EntityManagerInterface::class);
        $entityManager->shouldReceive('getRepository')->with(UsedString::class)->once()->andReturn($repository);
        $entityManager->shouldReceive('remove')->with(2)->once();
        $entityManager->shouldReceive('remove')->with(1)->once();
        $entityManager->shouldReceive('flush')->once();

        $tracker = new PasswordUsageTracker($entityManager, 3);
        $method->invoke($tracker, 1337);
    }

}

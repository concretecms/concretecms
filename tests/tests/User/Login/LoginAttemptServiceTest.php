<?php

namespace User\Login;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\LoginAttempt;
use Concrete\Core\Entity\User\LoginAttemptRepository;
use Concrete\Core\User\Login\LoginAttemptService;
use Doctrine\ORM\EntityManager;
use Concrete\Tests\TestCase;
use Mockery as M;
use Concrete\Core\Events\EventDispatcher;

class LoginAttemptServiceTest extends TestCase
{


    public function testPruneAttemptsFromConfig()
    {

        $fakeAttempt = M::mock(LoginAttempt::class);
        $fakeAttempt2 = M::mock(LoginAttempt::class);
        $config = M::mock(Repository::class);
        $repository = M::mock(LoginAttemptRepository::class);
        $director = M::mock(EventDispatcher::class);

        $em = M::mock(EntityManager::class);
        $em->shouldReceive('remove')->once()->with($fakeAttempt);
        $em->shouldReceive('remove')->once()->with($fakeAttempt2);
        $em->shouldReceive('flush')->once();

        $repository->shouldReceive('before')->andReturn([$fakeAttempt, $fakeAttempt2]);

        $config->shouldReceive('get')->andReturn(100);
        $em->shouldReceive('getRepository')->andReturn($repository);

        $service = new LoginAttemptService($em, $config, $director);
        $service->pruneAttempts();
    }

}

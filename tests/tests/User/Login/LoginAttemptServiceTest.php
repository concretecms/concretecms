<?php

namespace User\Login;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\LoginAttemptRepository;
use Concrete\Core\User\Login\LoginAttemptService;
use Doctrine\ORM\EntityManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;

class LoginAttemptServiceTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;


    public function testPruneAttemptsFromConfig()
    {
        $em = M::mock(EntityManager::class);
        $config = M::mock(Repository::class);
        $repository = M::mock(LoginAttemptRepository::class);

        $config->shouldReceive('get')->andReturn(100);
        $em->shouldReceive('getRepository')->andReturn($repository);

        $service = new LoginAttemptService($em, $config);
        $service->pruneAttempts();
    }

}

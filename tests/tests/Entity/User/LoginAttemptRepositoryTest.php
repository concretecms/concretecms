<?php

namespace Concrete\Tests\Entity\User;

use Carbon\Carbon;
use Concrete\Core\Entity\User\LoginAttemptRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit_Framework_TestCase;

class LoginAttemptRepositoryTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPruneInvalidTime()
    {
        $em = M::mock(EntityManager::class);
        $class = M::mock(ClassMetadata::class);

        $repository = new LoginAttemptRepository($em, $class);
        $repository->pruneAttempts(Carbon::now('PST'));
    }

    /**
     * Make sure we can run the prune method successfully
     */
    public function testPrune()
    {
        $em = M::mock(EntityManager::class);
        $class = M::mock(ClassMetadata::class);

        // Mock the query builder
        $query = M::mock('stdClass');
        $query->shouldReceive('execute')->once()->andReturn(true);

        $qb = M::mock(QueryBuilder::class);
        $qb->shouldIgnoreMissing($qb);
        $qb->shouldReceive('getQuery')->once()->andReturn($query);

        // Make the entity manager return our query builder mock
        $em->shouldReceive('createQueryBuilder')->andReturn($qb);

        // Run the method
        $repository = new LoginAttemptRepository($em, $class);
        $repository->pruneAttempts(Carbon::now('UTC'));
    }


}

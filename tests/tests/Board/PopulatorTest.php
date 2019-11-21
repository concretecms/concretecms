<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Board\Populator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManager;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class PopulatorTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testPopulate()
    {
        $entityManager = M::mock(EntityManager::class)->makePartial();
        $board = M::mock(Board::class);
        
        $populator = new Populator($entityManager);
        $populator->populate($board);
    }



}

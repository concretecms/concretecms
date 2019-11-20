<?php
namespace Concrete\Core\Board;

use Concrete\Core\Entity\Board\Board;
use Doctrine\ORM\EntityManager;

class Populator
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function reset(Board $board)
    {
        
    }
    
    public function populate(Board $board)
    {
        
    }

}

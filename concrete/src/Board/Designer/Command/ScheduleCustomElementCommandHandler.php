<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElement;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class ScheduleCustomElementCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ScheduleCustomElementCommand $command)
    {

    }

    
}

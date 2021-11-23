<?php

namespace Concrete\Core\Entity\Command;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Doctrine\ORM\EntityRepository;

class ProcessRepository extends EntityRepository
{

    public function findRunning()
    {
        $query = $this->getEntityManager()->createQuery(
            'select p from \Concrete\Core\Entity\Command\Process p where p.dateCompleted is null order by p.dateStarted asc'
        );
        return $query->getResult();
    }



}

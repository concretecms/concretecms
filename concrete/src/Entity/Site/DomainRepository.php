<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\EntityRepository;

class DomainRepository extends EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(
            array('domainID' => $id)
        );
    }


}

<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Site\Domain>
 */
class DomainRepository extends EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(
            array('domainID' => $id)
        );
    }


}

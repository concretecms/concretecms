<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\ORM\EntityRepository;

class RelationRepository extends EntityRepository
{
    public function findByGroupID($gID)
    {
        return $this->findBy(
            array('gID' => $gID)
        );
    }


}

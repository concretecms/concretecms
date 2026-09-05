<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Site\Group\Relation>
 */
class RelationRepository extends EntityRepository
{
    public function findByGroupID($gID)
    {
        return $this->findBy(
            array('gID' => $gID)
        );
    }


}

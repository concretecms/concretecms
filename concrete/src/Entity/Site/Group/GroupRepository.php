<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\ORM\EntityRepository;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Concrete\Core\Entity\Site\Group\Group>
 */
class GroupRepository extends EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(
            array('siteGID' => $id)
        );
    }


}

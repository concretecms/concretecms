<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(
            array('siteGID' => $id)
        );
    }


}

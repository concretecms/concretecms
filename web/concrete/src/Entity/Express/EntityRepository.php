<?php
namespace Concrete\Core\Entity\Express;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByIncludeInPublicList($find = null)
    {
        if ($find === null) {
            return $this->findBy(array());
        } else {
            $find = $find ? true : false;
        }
        return $this->findBy(array('include_in_public_list' => $find));
    }
}

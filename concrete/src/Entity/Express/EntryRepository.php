<?php
namespace Concrete\Core\Entity\Express;

/**
 * @since 8.0.0
 */
class EntryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByID($id)
    {
        return $this->findOneBy(array('exEntryID' => $id));
    }
}

<?php

namespace Concrete\Core\Attribute\Category;

class UserCategory extends AbstractCategory
{

    public function getByHandle($handle)
    {
        $query = 'select u from \Concrete\Core\Entity\User\Attribute u join u.attribute a
         where a.akHandle = :handle';
        $query = $this->entityManager->createQuery($query);
        $query->setParameter('handle', $handle);
        return $query->getOneOrNullResult();
    }



}

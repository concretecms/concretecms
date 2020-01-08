<?php

namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Entity\Express\EntityRepository;

class TemplateRepository extends EntityRepository
{

    public function findByCategory(Category $category)
    {
        $qb = $this->createQueryBuilder("t")
            ->where(':category member of t.categories')
            ->setParameters(array('category' => $category))
        ;
        return $qb->getQuery()->getResult();
    }
    
}

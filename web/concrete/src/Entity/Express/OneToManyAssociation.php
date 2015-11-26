<?php

namespace Concrete\Core\Entity\Express;

/**
 * @Entity
 */
class OneToManyAssociation extends Association
{

    public function getAnnotation()
    {
        return 'OneToMany';
    }


}
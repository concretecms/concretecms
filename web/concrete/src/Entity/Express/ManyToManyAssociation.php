<?php

namespace Concrete\Core\Entity\Express;

/**
 * @Entity
 */
class ManyToManyAssociation extends Association
{

    public function getAnnotation()
    {
        return 'ManyToMany';
    }


}
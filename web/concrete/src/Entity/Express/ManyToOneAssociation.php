<?php

namespace Concrete\Core\Entity\Express;

/**
 * @Entity
 */
class ManyToOneAssociation extends Association
{

    public function getAnnotation()
    {
        return 'ManyToOne';
    }


}
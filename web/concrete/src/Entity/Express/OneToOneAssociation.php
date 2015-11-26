<?php

namespace Concrete\Core\Entity\Express;

/**
 * @Entity
 */
class OneToOneAssociation extends Association
{

    public function getAnnotation()
    {
        return 'OneToOne';
    }


}
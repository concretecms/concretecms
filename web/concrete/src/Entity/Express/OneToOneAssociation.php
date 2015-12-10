<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Builder\OneToOneAssociationBuilder;
use Concrete\Core\Express\Association\Formatter\OneToOneFormatter;

/**
 * @Entity
 */
class OneToOneAssociation extends Association
{

    const TYPE_OWNING = 'owning';
    const TYPE_INVERSE = 'inverse';

    /**
     * @Column(type="string")
     */
    protected $association_type;

    /**
     * @return mixed
     */
    public function getAssociationType()
    {
        return $this->association_type;
    }

    /**
     * @param mixed $association_type
     */
    public function setAssociationType($association_type)
    {
        $this->association_type = $association_type;
    }

    public function getAssociationBuilder()
    {
        return new OneToOneAssociationBuilder($this);
    }

    public function getFormatter()
    {
        return new OneToOneFormatter($this);
    }



}
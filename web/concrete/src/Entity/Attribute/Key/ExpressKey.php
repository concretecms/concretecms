<?php
namespace Concrete\Core\Entity\Attribute\Key;

/**
 * @Entity
 * @Table(name="ExpressAttributeKeys")
 */
class ExpressKey extends Key
{

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entity")
     **/
    protected $entity;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }


}

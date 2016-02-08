<?php
namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(
 *     name="ExpressEntityEntryAttributeValues"
 * )
 */
class ExpressValue extends Value
{
    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @JoinColumn(name="exEntryID", referencedColumnName="exEntryID"),
     */
    protected $entry;

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }


}

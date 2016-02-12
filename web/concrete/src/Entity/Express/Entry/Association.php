<?php
namespace Concrete\Core\Entity\Express\Entry;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="ExpressEntityEntryAssociations")
 */
abstract class Association
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @JoinColumn(name="exEntryID", referencedColumnName="exEntryID")
     */
    protected $entry;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Association")
     */
    protected $association;

    /**
     * @return mixed
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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

    abstract public function getSelectedEntries();

}

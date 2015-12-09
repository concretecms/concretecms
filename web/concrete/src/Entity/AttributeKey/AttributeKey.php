<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\PackageTrait;


/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="AttributeKeyEntities")
 */
abstract class AttributeKey implements AttributeKeyInterface
{

    use PackageTrait;

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $akID;

    /**
     * @Column(type="string")
     */
    protected $akHandle;

    /**
     * @Column(type="string")
     */
    protected $akName;

    /**
     * @Column(type="boolean")
     */
    protected $akIsSearchable = true;

    /**
     * @Column(type="boolean")
     */
    protected $akIsInternal = false;

    /**
     * @Column(type="boolean")
     */
    protected $akIsSearchableIndexed = false;

    /**
     * @Column(type="boolean")
     */
    protected $akIsColumnHeader = true;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->akID;
    }

    /**
     * @param mixed $akID
     */
    public function setId($akID)
    {
        $this->akID = $akID;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->akHandle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($akHandle)
    {
        $this->akHandle = $akHandle;
    }

    /**
     * @return mixed
     */
    public function getIsInternal()
    {
        return $this->akIsInternal;
    }

    /**
     * @param mixed $is_internal
     */
    public function setIsInternal($akIsInternal)
    {
        $this->akIsInternal = $akIsInternal;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->akName;
    }

    /**
     * @param mixed $name
     */
    public function setName($akName)
    {
        $this->akName = $akName;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getIsSearchable()
    {
        return $this->akIsSearchable;
    }

    /**
     * @param mixed $is_searchable
     */
    public function setIsSearchable($akIsSearchable)
    {
        $this->akIsSearchable = $akIsSearchable;
    }

    /**
     * @return mixed
     */
    public function getIsIndexed()
    {
        return $this->akIsSearchableIndexed;
    }

    /**
     * @param mixed $is_indexed
     */
    public function setIsIndexed($akIsSearchableIndexed)
    {
        $this->akIsSearchableIndexed = $akIsSearchableIndexed;
    }

    /**
     * @return mixed
     */
    public function getIsColumnHeader()
    {
        return $this->akIsColumnHeader;
    }

    /**
     * @param mixed $akIsColumnHeader
     */
    public function setIsColumnHeader($akIsColumnHeader)
    {
        $this->akIsColumnHeader = $akIsColumnHeader;
    }


    /**
     * @return
     */
    abstract public function getFieldMappingDefinition();

    abstract public function getController();

    abstract public function getTypeHandle();

    /**
     * @deprecated
     */
    public function render($view = 'view', $value = false, $return = false)
    {
        $at = Type::getByHandle($this->getTypeHandle());
        $resp = $at->render($view, $this, $value, $return);
        if ($return) {
            return $resp;
        } else {
            print $resp;
        }
    }

    public function getAttributeKeyHandle()
    {
        return $this->getHandle();
    }

    public function getAttributeKeyID()
    {
        return $this->getID();
    }

    public function getAttributeType()
    {
        return Type::getByHandle($this->getTypeHandle());
    }


}

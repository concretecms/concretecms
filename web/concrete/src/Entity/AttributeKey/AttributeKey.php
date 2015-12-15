<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
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
    public function getAttributeKeyID()
    {
        return $this->akID;
    }


    /**
     * @return mixed
     */
    public function getAttributeKeyHandle()
    {
        return $this->akHandle;
    }

    /**
     * @param mixed $handle
     */
    public function setAttributeKeyHandle($akHandle)
    {
        $this->akHandle = $akHandle;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyInternal()
    {
        return $this->akIsInternal;
    }

    /**
     * @param mixed $is_internal
     */
    public function setIsAttributeKeyInternal($akIsInternal)
    {
        $this->akIsInternal = $akIsInternal;
    }

    /**
     * @return mixed
     */
    public function getAttributeKeyName()
    {
        return $this->akName;
    }

    /**
     * @param mixed $name
     */
    public function setAttributeKeyName($akName)
    {
        $this->akName = $akName;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeySearchable()
    {
        return $this->akIsSearchable;
    }

    /**
     * @param mixed $is_searchable
     */
    public function setIsAttributeKeySearchable($akIsSearchable)
    {
        $this->akIsSearchable = $akIsSearchable;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyContentIndexed()
    {
        return $this->akIsSearchableIndexed;
    }

    /**
     * @param mixed $is_indexed
     */
    public function setIsAttributeKeyContentIndexed($akIsSearchableIndexed)
    {
        $this->akIsSearchableIndexed = $akIsSearchableIndexed;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyColumnHeader()
    {
        return $this->akIsColumnHeader;
    }

    /**
     * @param mixed $akIsColumnHeader
     */
    public function setIsAttributeKeyColumnHeader($akIsColumnHeader)
    {
        $this->akIsColumnHeader = $akIsColumnHeader;
    }


    /**
     * @return
     */
    abstract public function getAttributeValue();

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

    public function getAttributeKeyDisplayName($format = 'html')
    {
        $value = tc('AttributeKeyName', $this->getAttributeKeyName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }


    public function getAttributeType()
    {
        return Type::getByHandle($this->getTypeHandle());
    }

    public function getRequestLoader()
    {
        return new StandardRequestLoader();
    }


}

<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\AttributeKey;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="akCategory", type="string")
 * @Table(
 *     name="AttributeKeys",
 *     indexes={
 *     @Index(name="pkgID", columns={"pkgID"})
 *     }
 * )
 */
class Key implements AttributeKeyInterface, ExportableInterface
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
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Type\Type", mappedBy="key", cascade={"persist", "remove"})
     */
    protected $key_type;

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value", mappedBy="attribute_key", cascade={"remove"})
     * @JoinColumn(name="avID", referencedColumnName="avID")
     **/
    protected $attribute_values;


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
     * @return mixed
     */
    public function getAttributeKeyType()
    {
        return $this->key_type;
    }

    /**
     * @param mixed $type
     */
    public function setAttributeKeyType($key_type)
    {
        $this->key_type = $key_type;
    }

    public function getAttributeType()
    {
        return $this->getAttributeKeyType()->getAttributeType();
    }

    public function getController()
    {
        $type = $this->getAttributeKeyType();
        if ($type) {
            $controller = $type->getAttributeType()->getController();
            $controller->setAttributeKey($this);
            return $controller;
        }
    }

    public function getAttributeKeyCategoryHandle()
    {
        return false;
    }

    public function getExporter()
    {
        return new AttributeKey();
    }

    /**
     * @deprecated
     */
    public function saveAttributeForm($mixed)
    {
        $controller = $this->getController();
        $value = $controller->getAttributeValueFromRequest();
        $mixed->setAttribute($this, $value);

        return $value;
    }

    /**
     * @deprecated
     */
    public function render($view = 'view', $value = false, $return = false)
    {
        $resp = $this->getAttributeType()->render($view, $this, $value, $return);
        if ($return) {
            return $resp;
        } else {
            echo $resp;
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

    public function getAttributeSets()
    {
        return \Concrete\Core\Attribute\Set::getByAttributeKey($this);
    }

    public function inAttributeSet(Set $set)
    {
        $sets = $this->getAttributeSets();

        return in_array($set, $sets);
    }

    /**
     * Doctrine requires this for certain queries.
     *
     * @return mixed
     */
    public function __toString()
    {
        return (string) $this->getAttributeKeyID();
    }

}

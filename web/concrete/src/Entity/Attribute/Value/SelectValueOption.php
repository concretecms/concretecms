<?php
namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(name="SelectAttributeValueOptions")
 */
class SelectValueOption
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avSelectOptionID;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\SelectKey", inversedBy="options")
     * @JoinColumn(name="avSelectOptionID", referencedColumnName="akID")
     */
    protected $key;

    /**
     * @Column(type="boolean")
     */
    protected $isEndUserAdded = false;

    /**
     * @Column(type="string")
     */
    protected $value = '';

    /**
     * @return mixed
     */
    public function isEndUserAdded()
    {
        return $this->isEndUserAdded;
    }

    /**
     * @param mixed $isEndUserAdded
     */
    public function setIsEndUserAdded($isEndUserAdded)
    {
        $this->isEndUserAdded = $isEndUserAdded;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getOptionID()
    {
        return $this->avSelectOptionID;
    }

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setAttributeKey($key)
    {
        $this->key = $key;
    }







}

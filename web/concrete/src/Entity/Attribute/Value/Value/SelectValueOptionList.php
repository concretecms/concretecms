<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="SelectAttributeValueOptionLists")
 */
class SelectValueOptionList
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avSelectOptionListID;

    /**
     * @OneToMany(targetEntity="SelectValueOption", mappedBy="list", cascade={"all"})
     * @JoinColumn(name="avSelectOptionListID", referencedColumnName="avSelectOptionListID")
     */
    protected $options;

    /**
     * SelectValueOptionList constructor.
     * @param $options
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function contains(SelectValueOption $option)
    {
        $id = $option->getSelectAttributeOptionID();
        return count(array_filter($this->getOptions()->toArray(), function($option) use ($id) {
            return $option->getSelectAttributeOptionID() == $id;
        })) > 0;
    }

}

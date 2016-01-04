<?php

namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(
 *     name="AttributeSets",
 *     indexes={
 *     @Index(name="asHandle", columns={"asHandle"}),
 *     @Index(name="pkgID", columns={"pkgID"})
 *     }
 * )
 */
class Set
{

    use PackageTrait;

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\SetKey", mappedBy="set", cascade={"all"})
     * @OrderBy({"asDisplayOrder" = "ASC"})
     */
    protected $keys;

    /**
     * @ManyToOne(targetEntity="Category", inversedBy="set")
     * @JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")
     */
    protected $category;


    public function __construct()
    {
        $this->keys = new ArrayCollection();
    }

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $asID;

    /**
     * @Column(type="string")
     */
    protected $asHandle;

    /**
     * @Column(type="string")
     */
    protected $asName;

    /**
     * @Column(type="integer", options={"unsigned":true})
     */
    protected $asDisplayOrder = 0;

    /**
     * @Column(type="boolean")
     */
    protected $asIsLocked = false;

    /**
     * @return mixed
     */
    public function getAttributeKeys()
    {
        return $this->keys;
    }

    /**
     * @param mixed $keys
     */
    public function setAttributeKeys($keys)
    {
        $this->keys = $keys;
    }

    public function clearAttributeKeys()
    {
        $this->keys = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAttributeKeyCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setAttributeKeyCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getAttributeSetID()
    {
        return $this->asID;
    }


    /**
     * @return mixed
     */
    public function getAttributeSetHandle()
    {
        return $this->asHandle;
    }

    /**
     * @param mixed $asHandle
     */
    public function setAttributeSetHandle($asHandle)
    {
        $this->asHandle = $asHandle;
    }

    /**
     * @return mixed
     */
    public function getAttributeSetName()
    {
        return $this->asName;
    }

    /**
     * @param mixed $asName
     */
    public function setAttributeSetName($asName)
    {
        $this->asName = $asName;
    }

    /**
     * @return mixed
     */
    public function getAttributeSetDisplayOrder()
    {
        return $this->asDisplayOrder;
    }

    /**
     * @param mixed $asDisplayOrder
     */
    public function setAttributeSetDisplayOrder($asDisplayOrder)
    {
        $this->asDisplayOrder = $asDisplayOrder;
    }

    /**
     * @return mixed
     */
    public function isAttributeSetLocked()
    {
        return $this->asIsLocked;
    }

    /**
     * @param mixed $asIsLocked
     */
    public function setAttributeSetIsLocked($asIsLocked)
    {
        $this->asIsLocked = $asIsLocked;
    }

    public function getSetManager()
    {
        $manager = \Core::make('Concrete\Core\Attribute\SetManager');
        $manager->setAttributeSet($this);
        return $manager;
    }





}


<?php
namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Export\Item\AttributeSet;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="AttributeSets",
 *     indexes={
 *     @ORM\Index(name="asHandle", columns={"asHandle"}),
 *     @ORM\Index(name="pkgID", columns={"pkgID"})
 *     }
 * )
 */
class Set implements ExportableInterface
{
    use PackageTrait;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\SetKey", mappedBy="set", cascade={"all"})
     * @ORM\OrderBy({"asDisplayOrder" = "ASC"})
     */
    protected $keys;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="sets")
     * @ORM\JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")
     */
    protected $category;

    public function __construct()
    {
        $this->keys = new ArrayCollection();
    }

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $asID;

    /**
     * @ORM\Column(type="string")
     */
    protected $asHandle;

    /**
     * @ORM\Column(type="string")
     */
    protected $asName;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $asDisplayOrder = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $asIsLocked = false;

    /**
     * @return mixed
     */
    public function getAttributeKeyCollection()
    {
        return $this->keys;
    }

    public function getExporter()
    {
        return new AttributeSet();
    }

    /**
     * @return Key
     */
    public function getAttributeKeys()
    {
        $keys = array();
        foreach($this->keys as $set_key) {
            $keys[] = $set_key->getAttributeKey();
        }
        return $keys;
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
     * @return mixed
     */
    public function getAttributeSetKeyCategoryID()
    {
        return $this->category->getAttributeKeyCategoryID();
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

    public function getAttributeSetDisplayName($format = 'html')
    {
        $value = tc('AttributeSetName', $this->getAttributeSetName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function addKey(Key $key)
    {
        $setKey = new SetKey();
        $setKey->setAttributeKey($key);
        $setKey->setAttributeSet($this);
        $setKey->setDisplayOrder(count($this->keys));
        $this->keys->add($setKey);
    }

    public function __toString()
    {
        return (string) $this->getAttributeSetID();
    }
}

<?php
namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="AttributeKeyCategories", indexes={@ORM\Index(name="pkgID", columns={"pkgID", "akCategoryID"}),
 * @ORM\Index(name="akCategoryHandle", columns={"akCategoryHandle"})})
 */
class Category implements CategoryObjectInterface
{
    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->sets = new ArrayCollection();
    }

    use PackageTrait;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $akCategoryID;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $akCategoryHandle;

    /**
     * @ORM\Column(type="integer")
     */
    protected $akCategoryAllowSets;

    /**
     * @ORM\ManyToMany(targetEntity="Type", inversedBy="categories")
     * @ORM\JoinTable(name="AttributeTypeCategories",
     * joinColumns={@ORM\JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")},
     * inverseJoinColumns={@ORM\JoinColumn(name="atID", referencedColumnName="atID")}
     * )
     */
    protected $types;

    /**
     * @ORM\OneToMany(targetEntity="Set", mappedBy="category", cascade={"remove"})
     * @ORM\OrderBy({"asDisplayOrder" = "ASC"})
     * @ORM\JoinColumn(name="akCategoryID", referencedColumnName="asID")
     */
    protected $sets;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key", mappedBy="category", cascade={"remove"})
     * @ORM\JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")
     */
    protected $keys;

    /**
     * @return mixed
     */
    public function getAttributeKeyCategoryID()
    {
        return $this->akCategoryID;
    }

    /**
     * @return mixed
     */
    public function getAttributeKeyCategoryHandle()
    {
        return $this->akCategoryHandle;
    }

    /**
     * @param mixed $akCategoryHandle
     */
    public function setAttributeKeyCategoryHandle($akCategoryHandle)
    {
        $this->akCategoryHandle = $akCategoryHandle;
    }

    /**
     * @return int one of the \Concrete\Core\Attribute\StandardSetManager::ASET_ALLOW_... constants
     */
    public function allowAttributeSets()
    {
        return $this->akCategoryAllowSets;
    }

    /**
     * @param int $akCategoryAllowSets one of the \Concrete\Core\Attribute\StandardSetManager::ASET_ALLOW_... constants
     */
    public function setAllowAttributeSets($akCategoryAllowSets)
    {
        $this->akCategoryAllowSets = $akCategoryAllowSets;
    }

    /**
     * @return CategoryInterface
     */
    public function getController()
    {
        $manager = \Core::make('manager/attribute/category');
        try {
            $controller = $manager->driver($this->getAttributeKeyCategoryHandle());
        } catch(\Exception $e) {
            $controller = $manager->driver('legacy');
        }
        $controller->setCategoryEntity($this);
        return $controller;
    }

    public function clearAttributeKeyCategoryTypes()
    {
        $this->types = new ArrayCollection();
    }

    public function getAttributeKeyCategory()
    {
        return $this->getController();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Attribute\Set[]
     */
    public function getAttributeSets()
    {
        return $this->sets;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Attribute\Type[]
     */
    public function getAttributeTypes()
    {
        return $this->types;
    }

    /**
     * @param mixed $types
     */
    public function setAttributeTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @deprecated
     *
     * @throws \RuntimeException if the category controller doesn't support the attribute sets
     */
    public function addSet($handle, $name, $pkg = null)
    {
        $controller = $this->getController();
        if (!method_exists($controller, 'addSet')) {
            throw new \RuntimeException(t('The attribute category %s does not support the attribute sets.', $this->getAttributeKeyCategoryHandle()));
        }

        return $controller->addSet($handle, $name, $pkg, false);
    }

    public function __toString()
    {
        return (string) $this->getAttributeKeyCategoryID();
    }

    /**
     * @deprecated
     *
     * @throws \RuntimeException if the category controller doesn't support associating the attribute types
     */
    public function associateAttributeKeyType(Type $type)
    {
        $controller = $this->getController();
        if (!method_exists($controller, 'associateAttributeKeyType')) {
            throw new \RuntimeException(t('The attribute category %s does not support associating the attribute types.', $this->getAttributeKeyCategoryHandle()));
        }
        $controller->associateAttributeKeyType($type);
    }


}

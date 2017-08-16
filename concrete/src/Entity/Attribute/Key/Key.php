<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\View;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Attribute\Key\SearchIndexer\StandardSearchIndexer;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\AttributeKey;
use Concrete\Core\Form\Control\ControlInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="akCategory", type="string")
 * @ORM\EntityListeners({"\Concrete\Core\Attribute\Key\Listener"})
 * @ORM\Table(
 *     name="AttributeKeys",
 *     indexes={
 *     @ORM\Index(name="pkgID", columns={"pkgID"})
 *     }
 * )
 */
class Key implements AttributeKeyInterface, ExportableInterface, ControlInterface
{
    use PackageTrait;

    protected $settings;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $akID;

    /**
     * @ORM\Column(type="string")
     */
    protected $akHandle;

    /**
     * @ORM\Column(type="string")
     */
    protected $akName;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akIsSearchable = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akIsInternal = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akIsSearchableIndexed = false;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\SetKey", mappedBy="attribute_key", cascade={"remove"}),
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $set_keys;

    /**
     * @var \Concrete\Core\Entity\Attribute\Type
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Type"),
     * @ORM\JoinColumn(name="atID", referencedColumnName="atID")
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Category", inversedBy="keys")
     * @ORM\JoinColumn(name="akCategoryID", referencedColumnName="akCategoryID")
     **/
    protected $category;

    public function setAttributeKeyID($akID)
    {
        $this->akID = $akID;
    }

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
    public function getAttributeKeySettings()
    {
        if (isset($this->settings)) {
            return $this->settings;
        }

        return $this->getController()->getAttributeKeySettings();
    }

    /**
     * @param mixed $type
     */
    public function setAttributeKeySettings($settings)
    {
        $this->settings = $settings; // This allows us to pass it around more easily
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Type $type
     */
    public function setAttributeType($type)
    {
        $this->type = $type;
    }

    public function getAttributeType()
    {
        return $this->type;
    }

    public function getAttributeTypeHandle()
    {
        return $this->type->getAttributeTypeHandle();
    }

    public function getController()
    {
        $controller = $this->type->getController();
        $controller->setAttributeKey($this);
        return $controller;
    }

    public function getAttributeKeyCategoryHandle()
    {
        return false;
    }

    public function getAttributeCategoryEntity()
    {
        return $this->category;
    }

    public function getAttributeCategory()
    {
        $manager = \Core::make('manager/attribute/category');
        $category = $manager->driver($this->getAttributeKeyCategoryHandle());
        if (is_object($this->category)) {
            $category->setCategoryEntity($this->category);
        }
        return $category;
    }

    /**
     * @param mixed $category
     */
    public function setAttributeCategoryEntity($category)
    {
        $this->category = $category;
    }


    public function getExporter()
    {
        return new AttributeKey();
    }

    public function getControlView(ContextInterface $context)
    {
        return $this->getController()->getControlView($context);
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

    public function getSearchIndexer()
    {
        return \Core::make('Concrete\Core\Attribute\Key\SearchIndexer\StandardSearchIndexer');
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

    /**
     * @deprecated
     */
    public function setAttributeSet($set)
    {
        if (!is_object($set)) {
            $set = Set::getByHandle($set);
        }
        $set->addKey($this);
    }

    /**
     * @deprecated
     */
    public function getAttributeValueIDList()
    {
        return array();
    }

}

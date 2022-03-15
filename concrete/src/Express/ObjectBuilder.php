<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator;
use Doctrine\ORM\EntityManagerInterface;

class ObjectBuilder
{
    protected $attributeTypeFactory;
    protected $entity;
    protected $entityManager;
    protected $app;

    /**
     * @return TypeFactory
     */
    public function getAttributeTypeFactory()
    {
        return $this->attributeTypeFactory;
    }

    public function __construct(
        Application $app,
        EntityManagerInterface $entityManager,
        TypeFactory $attributeTypeFactory)
    {
        $this->app = $app;
        $this->attributeTypeFactory = $attributeTypeFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function createObject($name)
    {
        $this->entity = new Entity();
        $this->entity->setName($name);

        return $this;
    }

    public function __call($method, $arguments)
    {
        $r = call_user_func_array(array($this->entity, $method), $arguments);
        if ($r !== null) {
            return $r; // handle the get* methods
        }
        return $this; // set methods return the object builder so it can chain.
    }

    public function save()
    {
        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();

        // grab and persist all attribute key settings object
        $category = $this->entity->getAttributeKeyCategory();
        foreach($this->entity->getAttributes() as $key) {
            $settings = $key->getAttributeKeySettings();
            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            $category->getSearchIndexer()->updateRepositoryColumns($category, $key);
        }

        return $this->entity;
    }

    public function buildForm($formName)
    {
        return new ObjectBuilder\FormBuilder($this, $formName);
    }

    public function buildAssociation()
    {
        return new ObjectBuilder\AssociationBuilder(new ObjectAssociationBuilder(), $this);
    }


    /**
     * Add an attribute to the object.
     *
     * @param string $type_handle Attribute Type Handle
     * @param string $name Attribute Key Name
     * @param string|null $handle Attribute Key Handle. If not specified, a new handle will created.
     *                            If attribute key with given handle already exists, just skip to add it.
     * @param Settings|null $settings Attribute Key Settings object, optional.
     * @param bool $akIsSearchable Make this attribute searchable or not in advanced search.
     *                             If not specified, searchable (default).
     * @param bool $akIsSearchableIndexed Include content of this attribute in search index.
     *                                    If not specified, do not included (default).
     * @return $this
     */
    public function addAttribute($type_handle, $name, $handle = null, Settings $settings = null, $akIsSearchable = true, $akIsSearchableIndexed = false)
    {
        if ($handle) {
            $existing = $this->entity->getAttributeKeyCategory()->getAttributeKeyByHandle($handle);
            if ($existing) {
                return $this;
            }
        }
        $key = new ExpressKey();
        $key->setEntity($this->entity);
        $type = $this->attributeTypeFactory->getByHandle($type_handle);
        if (!is_object($settings)) {
            $settings = $type->getController()->getAttributeKeySettings();
        }
        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);
        $key->setAttributeKeyName($name);
        $key->setAttributeType($type);
        if (!$handle) {
            $generator = new AttributeKeyHandleGenerator(
                $this->app->make(ExpressCategory::class, ['entity' => $this->entity])
            );
            $handle = $generator->generate($key);
        }
        $key->setAttributeKeyHandle($handle);
        $key->setIsAttributeKeySearchable($akIsSearchable);
        $key->setIsAttributeKeyContentIndexed($akIsSearchableIndexed);
        $this->entity->getAttributes()->add($key);
        return $this;
    }

    public function getObject()
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function buildObject()
    {
        $entity = $this->getObject();
        $this->entity = null;

        return $entity;
    }
}

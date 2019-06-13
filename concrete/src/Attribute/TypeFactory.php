<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Package;
use Concrete\Core\Foundation\Environment;
use Doctrine\ORM\EntityManager;
use Gettext\Translations;

/**
 * Factory class for creating and retrieving instances of the Attribute type entity.
 */
class TypeFactory
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Concrete\Core\Foundation\Environment
     */
    protected $environment;

    /**
     * @var \Concrete\Core\Attribute\Category\CategoryService
     */
    protected $categoryService;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Foundation\Environment $environment
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param CategoryService $categoryService
     */
    public function __construct(Environment $environment, EntityManager $entityManager, CategoryService $categoryService)
    {
        $this->environment = $environment;
        $this->entityManager = $entityManager;
        $this->categoryService = $categoryService;
    }

    /**
     * Search an attribute type given its handle.
     *
     * @param string $atHandle
     *
     * @return \Concrete\Core\Entity\Attribute\Type|null
     */
    public function getByHandle($atHandle)
    {
        $atHandle = (string) $atHandle;
        if ($atHandle === '') {
            return null;
        }
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findOneBy(['atHandle' => $atHandle]);
    }

    /**
     * Get the list of attribute types defined by a package.
     *
     * @param \Concrete\Core\Entity\Package $package
     *
     * @return \Concrete\Core\Entity\Attribute\Type[]
     */
    public function getListByPackage(Package $package)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findByPackage($package);
    }

    /**
     * Search an attribute type given its id.
     *
     * @param int $atID
     *
     * @return \Concrete\Core\Entity\Attribute\Type|null
     */
    public function getByID($atID)
    {
        $atID = (int) $atID;
        if ($atID === 0) {
            return null;
        }
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findOneBy(['atID' => $atID]);
    }

    /**
     * Create a new attribute type.
     *
     * @param string $atHandle The handle of the new attribute type
     * @param string $atName The name of the new attribute type
     * @param \Concrete\Core\Entity\Package|null $pkg The package defining the attribute type (if any)
     *
     * @return \Concrete\Core\Entity\Attribute\Type
     */
    public function add($atHandle, $atName, $pkg = null)
    {
        $type = new AttributeType();
        $type->setAttributeTypeName($atName);
        $type->setAttributeTypeHandle($atHandle);
        if ($pkg) {
            $type->setPackage($pkg);
        }

        $this->installDatabase($type);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    /**
     * Get the list of attribute types.
     *
     * @param string|false|null $akCategoryHandle The handle of the attribute category (if falsy, all the attribute types will be returned)
     *
     * @return \Concrete\Core\Entity\Attribute\Type[]
     */
    public function getList($akCategoryHandle = false)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');
        $akCategoryHandle = (string) $akCategoryHandle;
        if ($akCategoryHandle === '') {
            return $r->findAll();
        }
        $category = $this->categoryService->getByHandle($akCategoryHandle);

        return $category->getAttributeTypes();
    }

    /**
     * @deprecated use the getList method (same arguments and same results)
     *
     * @param mixed $akCategoryHandle
     *
     * @return \Concrete\Core\Entity\Attribute\Type[]
     */
    public function getAttributeTypeList($akCategoryHandle = false)
    {
        return $this->getList($akCategoryHandle);
    }

    /**
     * @deprecated
     */
    public function exportTranslations()
    {
        $translations = new Translations();
        foreach ($this->getList() as $type) {
            $translations->insert('AttributeTypeName', $type->getAttributeTypeName());
        }

        return $translations;
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Type $type
     */
    protected function installDatabase(AttributeType $type)
    {
        $r = $this->environment->getRecord(DIRNAME_ATTRIBUTES . '/' . $type->getAttributeTypeHandle() . '/' . FILENAME_ATTRIBUTE_DB, $type->getPackageHandle());
        if ($r->exists()) {
            // db.xml legacy approach
            \Concrete\Core\Package\Package::installDB($r->file);
        }

        if (is_dir(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' .
            DIRNAME_ENTITIES)) {
            // Refresh the application entities
            $manager = new DatabaseStructureManager($this->entityManager);
            $manager->refreshEntities();
        }
    }
}

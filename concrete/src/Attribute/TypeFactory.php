<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Entity\Package;
use Concrete\Core\Foundation\Environment;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Gettext\Translations;

/**
 * Factory class for creating and retrieving instances of the Attribute type entity.
 */
class TypeFactory
{
    protected $entityManager;
    protected $environment;

    public function __construct(Environment $environment, EntityManager $entityManager)
    {
        $this->environment = $environment;
        $this->entityManager = $entityManager;
    }

    public function getByHandle($atHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findOneBy(array('atHandle' => $atHandle));
    }

    public function getListByPackage(Package $package)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findByPackage($package);
    }

    public function getByID($atID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');

        return $r->findOneBy(array('atID' => $atID));
    }

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

    public function getList($akCategoryHandle = false)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');
        if ($akCategoryHandle == false) {
            return $r->findAll();
        } else {
            $category = Category::getByHandle($akCategoryHandle);

            return $category->getAttributeTypes();
        }
    }

    /**
     * @deprecated
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
        foreach($this->getList() as $type) {
            $translations->insert('AttributeTypeName', $type->getAttributeTypeName());
        }
        return $translations;
    }

}

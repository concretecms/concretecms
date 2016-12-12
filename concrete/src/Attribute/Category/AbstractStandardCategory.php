<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractStandardCategory extends AbstractCategory implements StandardCategoryInterface
{

    use StandardCategoryTrait {
        delete as deleteCategory;
    }

    public function delete()
    {
        parent::delete();
        $this->deleteCategory();
    }

    public function add($type, $key, $settings = null, $pkg = null)
    {
        /**
         * @var $key Key
         */
        $key = parent::add($type, $key, $settings, $pkg);
        $key->setAttributeCategoryEntity($this->getCategoryEntity());
        $this->entityManager->persist($key);
        $this->entityManager->flush();
        return $key;
    }

}

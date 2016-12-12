<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Category;
use Gettext\Translations;

defined('C5_EXECUTE') or die("Access Denied.");

class CategoryService
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByHandle($akCategoryHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findOneBy(array('akCategoryHandle' => $akCategoryHandle));
    }

    public function getByID($akCategoryID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findOneBy(array('akCategoryID' => $akCategoryID));
    }

    public function getList()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findAll();
    }

    public function getListByPackage(Package $pkg)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findByPackage($pkg);
    }


    public function add($akCategoryHandle, $allowSets = StandardSetManager::ASET_ALLOW_SINGLE, $pkg = null)
    {
        $category = new Category();
        $category->setAttributeKeyCategoryHandle($akCategoryHandle);
        $category->setAllowAttributeSets($allowSets);
        if ($pkg) {
            $category->setPackage($pkg);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $indexer = $category->getController()->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->createRepository($category->getController());
        }

        return $category->getController();
    }

    /**
     * @deprecated
     */
    public function exportTranslations()
    {
        $translations = new Translations();
        $list = $this->getList();

        $akcNameMap = array(
            'collection' => 'Page attributes',
            'user' => 'User attributes',
            'file' => 'File attributes',
        );

        foreach($list as $category) {
            $akcHandle = $category->getAttributeKeyCategoryHandle();
            $translations->insert('AttributeKeyCategory', isset($akcNameMap[$akcHandle]) ? $akcNameMap[$akcHandle] : ucwords(str_replace(array('_', '-', '/'), ' ', $akcHandle)));
        }
        return $translations;
    }

}

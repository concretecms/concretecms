<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Gettext\Translations;

defined('C5_EXECUTE') or die('Access Denied.');

class CategoryService
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get a attribute category given its handle.
     *
     * @param string $akCategoryHandle
     *
     * @return Category|null
     */
    public function getByHandle($akCategoryHandle)
    {
        $r = $this->entityManager->getRepository(Category::class);

        return $r->findOneBy(['akCategoryHandle' => $akCategoryHandle]);
    }

    /**
     * Get a attribute category given its ID.
     *
     * @param int $akCategoryID
     *
     * @return Category|null
     */
    public function getByID($akCategoryID)
    {
        $r = $this->entityManager->getRepository(Category::class);

        return $r->findOneBy(['akCategoryID' => $akCategoryID]);
    }

    /**
     * Get all the available attribute categories.
     *
     * @return Category[]
     */
    public function getList()
    {
        $r = $this->entityManager->getRepository(Category::class);

        return $r->findAll();
    }

    /**
     * Get all the available attribute categories created by a package.
     *
     * @param Package $pkg
     *
     * @return Category[]
     */
    public function getListByPackage(Package $pkg)
    {
        $r = $this->entityManager->getRepository(Category::class);

        return $r->findByPackage($pkg);
    }

    /**
     * Create a new attribute category.
     *
     * @param string $akCategoryHandle the category handle
     * @param int $allowSets One of the StandardSetManager::ASET_ALLOW_... constants
     * @param Package|null $pkg the package that is creating this category
     *
     * @return CategoryInterface
     */
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

        $akcNameMap = [
            'collection' => 'Page attributes',
            'user' => 'User attributes',
            'file' => 'File attributes',
        ];

        foreach($list as $category) {
            $akcHandle = $category->getAttributeKeyCategoryHandle();
            $translations->insert('AttributeKeyCategory', isset($akcNameMap[$akcHandle]) ? $akcNameMap[$akcHandle] : ucwords(str_replace(['_', '-', '/'], ' ', $akcHandle)));
        }

        return $translations;
    }
}

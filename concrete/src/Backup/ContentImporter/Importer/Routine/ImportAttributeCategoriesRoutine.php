<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Support\Facade\Application;
use SimpleXMLElement;

class ImportAttributeCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attribute_categories';
    }

    public function import(SimpleXMLElement $xRoot)
    {
        if (isset($xRoot->attributecategories)) {
            $app = Application::getFacadeApplication();

            $categoryService = $app->make(CategoryService::class);
            /* @var CategoryService $categoryService */

            foreach ($xRoot->attributecategories->category as $xCategory) {
                $acHandle = (string) $xCategory['handle'];
                $category = $categoryService->getByHandle($acHandle);
                if ($category === null) {
                    $pkg = static::getPackageObject($xCategory['package']);
                    $categoryService->add(
                        $acHandle,
                        (int) $xCategory['allow-sets'],
                        $pkg
                    );
                    $category = $categoryService->getByHandle($acHandle);
                }
                if (isset($xCategory->attributetypes)) {
                    $categoryTypes = $category->getAttributeTypes();
                    $save = false;
                    foreach ($xCategory->attributetypes->attributetype as $xAttributeType) {
                        $type = $typeFactory->getByHandle((string) $xAttributeType['handle']);
                        if ($type !== null && !$categoryTypes->contains($type)) {
                            $categoryTypes->add($type);
                            $save = true;
                        }
                    }
                    if ($save === true) {
                        $entityManager->flush($category);
                    }
                }
            }
        }
    }
}

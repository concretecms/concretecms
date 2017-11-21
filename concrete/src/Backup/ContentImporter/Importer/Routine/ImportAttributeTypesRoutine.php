<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportAttributeTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attribute_types';
    }

    public function import(SimpleXMLElement $xRoot)
    {
        if (isset($xRoot->attributetypes)) {
            $app = Application::getFacadeApplication();

            $categoryService = $app->make(CategoryService::class);
            /* @var CategoryService $categoryService */
            $typeFactory = $app->make(TypeFactory::class);
            /* @var TypeFactory $typeFactory */
            $entityManager = $app->make(EntityManagerInterface::class);
            /* @var EntityManagerInterface $entityManager */
            $textService = $app->make('helper/text');
            /* @var \Concrete\Core\Utility\Service\Text $textService */

            foreach ($xRoot->attributetypes->attributetype as $xAttributeType) {
                $atHandle = (string) $xAttributeType['handle'];
                $type = $typeFactory->getByHandle($atHandle);
                if ($type === null) {
                    $pkg = static::getPackageObject($xAttributeType['package']);
                    $atName = isset($xAttributeType['name']) ? (string) $xAttributeType['name'] : '';
                    if ($atName === '') {
                        $atName = $textService->unhandle($atHandle);
                    }
                    $type = $typeFactory->add($atHandle, $atName, $pkg);
                }
                if (isset($xAttributeType->categories)) {
                    foreach ($xAttributeType->categories->children() as $xAttributeCategory) {
                        $category = $categoryService->getByHandle((string) $xAttributeCategory['handle']);
                        if ($category !== null) {
                            $categoryTypes = $category->getAttributeTypes();
                            if (!$categoryTypes->contains($type)) {
                                $categoryTypes->add($type);
                                $entityManager->flush($category);
                            }
                        }
                    }
                }
            }
        }
    }
}

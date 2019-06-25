<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Support\Facade\Application;
use SimpleXMLElement;

class ImportAttributeSetsRoutine extends AbstractRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'attribute_sets';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::import()
     */
    public function import(SimpleXMLElement $sx)
    {
        if (isset($sx->attributesets)) {
            $app = Application::getFacadeApplication();
            $setFactory = $app->make(SetFactory::class);
            $categoryService = $app->make(CategoryService::class);
            foreach ($sx->attributesets->attributeset as $as) {
                $set = $setFactory->getByHandle((string) $as['handle']);
                $akc = $categoryService->getByHandle($as['category']);
                $controller = $akc->getController();
                $manager = $controller->getSetManager();
                if ($set === null) {
                    $pkg = static::getPackageObject($as['package']);
                    $set = $manager->addSet((string) $as['handle'], (string) $as['name'], $pkg, $as['locked']);
                }
                foreach ($as->children() as $ask) {
                    $ak = $controller->getAttributeKeyByHandle((string) $ask['handle']);
                    if ($ak) {
                        $keySets = $setFactory->getByAttributeKey($ak);
                        if (empty($keySets)) {
                            $manager->addKey($set, $ak);
                        }
                    }
                }
            }
        }
    }
}

<?php
namespace Concrete\Core\Board\Helper\Traits;

use Concrete\Core\Board\Instance\Slot\Content\AvailableObjectCollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentRenderer;
use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Entity\Board\SlotTemplate;
use Symfony\Component\HttpFoundation\JsonResponse;

trait SlotTemplateJsonHelperTrait
{

    /**
     * @param SlotTemplate[]
     * @param ItemObjectGroup[]
     * @return array
     */
    public function createSlotTemplateJsonArray(array $templates, array $itemObjectGroups) : array
    {
        $availableObjectCollectionFactory = app(AvailableObjectCollectionFactory::class);
        $renderer = app(ContentRenderer::class);
        $options = [];
        foreach($templates as $template) {
            $templateDriver = $template->getDriver();
            if ($templateDriver->getTotalContentSlots() == count($itemObjectGroups)) {
                $objectCollections = $availableObjectCollectionFactory
                    ->getObjectCollectionsForTemplate($template, $itemObjectGroups);
                if ($objectCollections) {
                    foreach ($objectCollections as $objectCollection) {
                        $options[] = [
                            'template' => $template,
                            'collection' => $objectCollection,
                            'content' => $renderer->render($objectCollection, $template)
                        ];
                    }
                }
            }
        }
        return $options;
    }

}

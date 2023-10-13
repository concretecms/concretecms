<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\Attribute\OpenApiSpecifiableInterface;
use Concrete\Core\Api\Events\GenerateApiSpecEvent;
use Concrete\Core\Api\OpenApi\Factory\ExpressEntitySpecFactory;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Express\ObjectManager;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;

class SpecGenerator implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var SpecMerger
     */
    protected $merger;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var SourceRegistry
     */
    protected $sourceRegistry;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher, ObjectManager $objectManager, SpecMerger $merger, SourceRegistry $sourceRegistry)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->objectManager = $objectManager;
        $this->merger = $merger;
        $this->sourceRegistry = $sourceRegistry;
    }

    private function addExpressSpec(OpenApi $openApi)
    {
        $objects = $this->objectManager->getEntities(true)->findBy(['include_in_rest_api' => true]);
        foreach ($objects as $object) {
            $factory = new ExpressEntitySpecFactory();
            $spec = $factory->build($object);
            $this->merger->merge($spec, $openApi);
        }

        return $openApi;
    }

    // Goes through all the models like UpdatedFile, etc... and makes sure their schemas
    // have valid, dynamic attributes based on the attributes defined.
    private function addCustomAttributesToModels(OpenApi $openApi)
    {
        $fileAttributes = $this->app->make(FileCategory::class)->getList();
        $pageAttributes = $this->app->make(PageCategory::class)->getList();
        $userAttributes = $this->app->make(UserCategory::class)->getList();
        foreach ($openApi->components->schemas as $schema) {
            if (in_array($schema->schema, ['UpdatedFile', 'UpdatedUser', 'UpdatedPage'])) {

                $propertiesProperty = new SpecProperty('attributes', 'Attributes', 'object');
                switch($schema->schema) {
                    case 'UpdatedFile':
                        $attributesToAdd = $fileAttributes;
                        break;
                    case 'UpdatedUser':
                        $attributesToAdd = $userAttributes;
                        break;
                    case 'UpdatedPage':
                        $attributesToAdd = $pageAttributes;
                        break;
                }

                foreach ($attributesToAdd as $attribute) {
                    $controller = $attribute->getController();
                    if ($controller instanceof OpenApiSpecifiableInterface) {
                        $attributeProperty = $controller->getOpenApiSpecProperty($attribute);
                    } else {
                        $attributeProperty = new SpecProperty(
                            $attribute->getAttributeKeyHandle(),
                            $attribute->getAttributeKeyDisplayName(),
                            'string'
                        );
                    }
                    $propertiesProperty->addObjectProperty($attributeProperty);
                }
                $this->merger->mergeProperty($propertiesProperty, $schema);
            }
        }
        return $openApi;
    }

    public function getSpec(): OpenApi
    {
        $r = Generator::scan($this->sourceRegistry->getSources());
        $r = $this->addExpressSpec($r);
        $r = $this->addCustomAttributesToModels($r);

        $event = new GenerateApiSpecEvent($r);
        $this->eventDispatcher->dispatch('on_api_spec_generate', $event);
        return $event->getOpenApi();
    }

}

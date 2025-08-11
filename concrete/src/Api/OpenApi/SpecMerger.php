<?php

namespace Concrete\Core\Api\OpenApi;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Schema;
use OpenApi\Serializer;

class SpecMerger
{

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function merge(SpecFragment $object, OpenApi $openApi): OpenApi
    {
        $mergeables = [];
        $pathCollection = $object->getPaths();
        if ($pathCollection) {
            $mergeables = $pathCollection->getPathsAsPathItems();
        }
        $openApi->merge($mergeables);

        // Merge custom models from Express objects:
        // AE: I'm not sure why it has to work this way. I couldn't get the merge flow above to work with Schema
        // Or with Component objects so I had to do something like this, which is uglier but does work to get the
        // Express models included with everything else.
        $components = $object->getComponents();
        if ($components) {
            $componentsObject = $this->serializer->deserialize(json_encode($components), 'OpenApi\Annotations\Components');
        }
        foreach ($componentsObject->schemas as $schema) {
            $openApi->components->schemas[] = $schema;
        }
        foreach ($componentsObject->requestBodies as $requestBody) {
            $openApi->components->requestBodies[] = $requestBody;
        }
        // End custom Express merge.

        // Security schemes merge
        // This is obnoxious but it's actually the easiest way to dynamically merge
        foreach ($object->getSecuritySchemes() as $securityScheme) {
            foreach ($openApi->components->securitySchemes as $openApiSecurityScheme) {
                if ($openApiSecurityScheme->securityScheme === $securityScheme->getName()) {
                    foreach ($securityScheme->getScopes() as $scope => $description) {
                        $openApiSecurityScheme->flows[0]->scopes[$scope] = $description;
                    }
                }
            }
        }

        return $openApi;
    }

    public function mergeProperty(SpecProperty $specProperty, Schema $schema)
    {
        $property = $this->serializer->deserialize(json_encode($specProperty), 'OpenApi\Annotations\Property');
        $schema->properties[] = $property;
        return $schema;
    }

}

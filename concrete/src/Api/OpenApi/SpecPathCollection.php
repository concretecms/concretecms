<?php

namespace Concrete\Core\Api\OpenApi;

use OpenApi\Serializer;

class SpecPathCollection
{

    /**
     * @var SpecPath[]
     */
    protected $paths;

    public function add(SpecPath $path)
    {
        $this->paths[] = $path;
    }

    #[\ReturnTypeWillChange]
    public function getPathsAsPathItems()
    {
        $pathArrays = [];
        $pathsToDeserialize = [];
        foreach ($this->paths as $path) {
            $pathArrays[] = $path->jsonSerialize();
        }
        // There is probably a more efficient way to do this. I'm trying to loop through all paths
        // and if the path already exists merge the path with the previous path but keep the HTTP verb.
        foreach ($pathArrays as $pathArray) {
            $pathMatched = false;
            foreach ($pathsToDeserialize as &$pathToDeserialize) {
                if ($pathToDeserialize['path'] == $pathArray['path']) {
                    foreach (['get', 'post', 'put', 'delete'] as $verb) {
                        if (isset($pathArray[$verb])) {
                            $pathMatched = true;
                            $pathToDeserialize[$verb] = $pathArray[$verb];
                        }
                    }
                }
            }
            if (!$pathMatched) {
                $pathsToDeserialize[] = $pathArray;
            }
        }

        $pathItems = [];
        $serializer = new Serializer();
        foreach ($pathsToDeserialize as $path) {
            $pathItems[] = $serializer->deserialize(json_encode($path), 'OpenApi\Annotations\PathItem');
        }
        return $pathItems;
    }
}

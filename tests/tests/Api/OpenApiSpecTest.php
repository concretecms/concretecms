<?php

namespace Concrete\Tests\Api;

use Concrete\Core\Api\OpenApi\SourceRegistry;
use Concrete\Tests\TestCase;
use OpenApi\Generator;

/**
 * The API clients (and the OAuth scopes of the installation) are built out of the generated OpenAPI
 * specification: a path that isn't there simply doesn't exist for them.
 */
class OpenApiSpecTest extends TestCase
{
    /**
     * @var \OpenApi\Annotations\OpenApi
     */
    private static $spec;

    /**
     * @return \OpenApi\Annotations\OpenApi
     */
    private function getSpec()
    {
        if (self::$spec === null) {
            $sourceRegistry = new SourceRegistry();
            $sourceRegistry->addDefaultSources();
            self::$spec = Generator::scan($sourceRegistry->getSources());
        }

        return self::$spec;
    }

    public function testTheBlockTypePathsAreInTheSpec(): void
    {
        $paths = [];
        foreach ($this->getSpec()->paths as $path) {
            $paths[] = $path->path;
        }

        $this->assertContains('/ccm/api/1.0/block_types', $paths);
        $this->assertContains('/ccm/api/1.0/block_types/{blockTypeHandle}', $paths);
    }

    public function testTheBlockTypesScopeIsInTheSpec(): void
    {
        $scopes = [];
        foreach ($this->getSpec()->components->securitySchemes as $scheme) {
            foreach ($scheme->flows[0]->scopes as $scope => $description) {
                $scopes[] = $scope;
            }
        }

        $this->assertContains('block_types:read', $scopes);
    }

    public function testEveryReferencedSchemaExists(): void
    {
        $defined = [];
        foreach ($this->getSpec()->components->schemas as $schema) {
            $defined[] = $schema->schema;
        }
        $referenced = [];
        foreach ($this->findRefs(json_decode(json_encode($this->getSpec()), true)) as $ref) {
            if (strpos($ref, '#/components/schemas/') === 0) {
                $referenced[] = substr($ref, strlen('#/components/schemas/'));
            }
        }

        $this->assertSame([], array_values(array_unique(array_diff($referenced, $defined))));
    }

    /**
     * @param mixed $data
     *
     * @return string[]
     */
    private function findRefs($data): array
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === '$ref' && is_string($value)) {
                    $result[] = $value;
                } else {
                    $result = array_merge($result, $this->findRefs($value));
                }
            }
        }

        return $result;
    }
}

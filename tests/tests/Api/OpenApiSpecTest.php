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

    public function testTheOpenApiPathIsInTheSpec(): void
    {
        $paths = [];
        foreach ($this->getSpec()->paths as $path) {
            $paths[] = $path->path;
        }

        $this->assertContains('/ccm/api/1.0/system/openapi', $paths);
    }

    public static function providerNewScopes(): array
    {
        return [
            ['block_types:read'],
            ['system:openapi:read'],
        ];
    }

    /**
     * @dataProvider providerNewScopes
     */
    public function testTheScopeIsInTheSpec(string $expectedScope): void
    {
        $scopes = [];
        foreach ($this->getSpec()->components->securitySchemes as $scheme) {
            foreach ($scheme->flows[0]->scopes as $scope => $description) {
                $scopes[] = $scope;
            }
        }

        $this->assertContains($expectedScope, $scopes);
    }

    /**
     * The clients generated out of the specification (the MCP server is one) turn a request body into
     * the input schema of an operation, and that has to be a plain object: a body described with oneOf
     * or anyOf makes them generate something they then refuse.
     */
    public function testTheRequestBodiesArePlainObjects(): void
    {
        $spec = json_decode(json_encode($this->getSpec()), true);
        $offending = [];
        foreach ($spec['components']['requestBodies'] ?? [] as $name => $requestBody) {
            foreach ($requestBody['content'] ?? [] as $mediaType => $content) {
                $schema = $content['schema'] ?? [];
                if (isset($schema['$ref'])) {
                    $schema = $spec['components']['schemas'][substr($schema['$ref'], strlen('#/components/schemas/'))] ?? [];
                }
                if (isset($schema['oneOf']) || isset($schema['anyOf'])) {
                    $offending[] = "{$name} ({$mediaType})";
                }
            }
        }

        $this->assertSame([], $offending);
    }

    /**
     * Every scope used by an operation must be declared in the security scheme it refers to, otherwise
     * it will never make it into the OAuth2Scope table (see SynchronizeScopesCommandHandler).
     */
    public function testEveryUsedScopeIsDeclared(): void
    {
        $declared = [];
        foreach ($this->getSpec()->components->securitySchemes as $scheme) {
            foreach ($scheme->flows[0]->scopes as $scope => $description) {
                $declared[$scheme->securityScheme][] = $scope;
            }
        }
        $undeclared = [];
        foreach ($this->getSpec()->paths as $path) {
            foreach (['get', 'post', 'put', 'delete'] as $method) {
                // swagger-php fills the unused properties with a placeholder string
                $operation = $path->{$method};
                if (!is_object($operation) || !is_array($operation->security)) {
                    continue;
                }
                foreach ($operation->security as $security) {
                    foreach ($security as $schemeName => $scopes) {
                        foreach ((array) $scopes as $scope) {
                            if (!in_array($scope, $declared[$schemeName] ?? [], true)) {
                                $undeclared[] = "{$path->path}: {$schemeName}/{$scope}";
                            }
                        }
                    }
                }
            }
        }

        $this->assertSame([], $undeclared);
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

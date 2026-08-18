<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Api\OpenApi\SpecGenerator;
use Concrete\Core\Http\Request;
use Concrete\Core\System\Info;
use Concrete\Core\System\InfoTransformer;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\Response;

class System
{
    /**
     * @var \Concrete\Core\Api\OpenApi\SpecGenerator
     */
    protected $specGenerator;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    public function __construct(SpecGenerator $specGenerator, Request $request)
    {
        $this->specGenerator = $specGenerator;
        $this->request = $request;
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/system/info",
     *     tags={"system"},
     *     operationId="getSystemInfo",
     *     security={
     *         {"clientCredentials": {"system:info:read"}}
     *     },
     *     @OA\Response(response="200", description="The info object in JSON format")
     * )
     */
    public function info()
    {
        return new Item(new Info(), new InfoTransformer());
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/system/openapi",
     *     tags={"system"},
     *     operationId="getOpenApiSpecification",
     *     summary="Get the OpenAPI specification of this installation.",
     *     security={
     *         {"clientCredentials": {"system:openapi:read"}},
     *         {"authorization": {"system:openapi:read"}}
     *     },
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="The format of the specification (it defaults to json)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"json", "yaml"}
     *         )
     *     ),
     *     @OA\Response(response="200", description="The OpenAPI specification of this installation")
     * )
     */
    public function openapi()
    {
        $spec = $this->specGenerator->getSpec();
        if (strtolower((string) $this->request->query->get('format', 'json')) === 'yaml') {
            return new Response($spec->toYaml(), Response::HTTP_OK, ['Content-Type' => 'application/yaml']);
        }

        return new Response($spec->toJson(), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

}

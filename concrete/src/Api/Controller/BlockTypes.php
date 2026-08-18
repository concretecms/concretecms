<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Fractal\Transformer\BlockTypeTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use League\Fractal\Resource\Collection;

defined('C5_EXECUTE') or die('Access Denied.');

class BlockTypes extends ApiController
{
    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/block_types",
     *     tags={"block_types"},
     *     operationId="getBlockTypes",
     *     summary="List the available block types, with the schema of the value they accept.",
     *     security={
     *         {"authorization": {"block_types:read"}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/BlockType")
     *         ),
     *     ),
     * )
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function listBlockTypes()
    {
        $blockTypeList = new BlockTypeList();

        return new Collection(
            $blockTypeList->get(),
            $this->app->make(BlockTypeTransformer::class),
            Resources::RESOURCE_BLOCK_TYPES
        );
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/block_types/{blockTypeHandle}",
     *     tags={"block_types"},
     *     operationId="getBlockTypeByHandle",
     *     summary="Find a block type by its handle, with the schema of the value it accepts.",
     *     security={
     *         {"authorization": {"block_types:read"}}
     *     },
     *     @OA\Parameter(
     *         name="blockTypeHandle",
     *         in="path",
     *         description="Handle of the block type to return",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/BlockType"),
     *     ),
     * )
     *
     * @return \League\Fractal\Resource\Item|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function read($blockTypeHandle)
    {
        $blockType = BlockType::getByHandle($blockTypeHandle);
        if ($blockType === null) {
            return $this->error(t('Block type not found.'), 404);
        }

        return $this->transform(
            $blockType,
            $this->app->make(BlockTypeTransformer::class),
            Resources::RESOURCE_BLOCK_TYPES
        );
    }
}

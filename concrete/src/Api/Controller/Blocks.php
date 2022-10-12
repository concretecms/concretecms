<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Block\Block;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Fractal\Transformer\BlockTransformer;
use Concrete\Core\Api\Resources;

class Blocks extends ApiController
{

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/blocks/{blockID}",
     *     tags={"blocks"},
     *     summary="Find a block by its ID",
     *     security={
     *         {"authorization": {"blocks:read"}}
     *     },
     *     @OA\Parameter(
     *         name="blockID",
     *         in="path",
     *         description="ID of block to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"pages"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Block"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to access this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     * )
     */
    public function read($bID)
    {
        $b = Block::getByID($bID);
        if ($b) {
            $checker = new Checker($b);
            if (!$checker->canViewBlock()) {
                return $this->error(t("You do not have access to view this block."), 401);
            }
        } else {
            return $this->error(t("Block not found"), 404);
        }

        return $this->transform($b, new BlockTransformer(), Resources::RESOURCE_BLOCKS);
    }

    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/blocks/{blockID}",
     *     tags={"blocks"},
     *     summary="Delete a block by its ID",
     *     security={
     *         {"authorization": {"blocks:delete"}}
     *     },
     *     @OA\Parameter(
     *         name="blockID",
     *         in="path",
     *         description="ID of block to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeletedResponse"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to delete this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Block not found"
     *     ),
     * )
     */
    public function delete($bID)
    {
        $b = Block::getByID($bID);
        if ($b) {
            $checker = new Checker($b);
            if (!$checker->canDeleteBlock()) {
                return $this->error(t("You do not have access to delete this block."), 401);
            }
        } else {
            return $this->error(t("Block not found"), 404);
        }

        $b->deleteBlock(true);
        return $this->deleted(Resources::RESOURCE_BLOCKS, $bID);
    }


}

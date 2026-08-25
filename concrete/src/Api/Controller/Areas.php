<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Area\Exception\AreaNotFoundException;
use Concrete\Core\Block\Exception\BlockNotFoundException;
use Concrete\Core\Block\Traits\GetBlockToEditTrait;
use Concrete\Core\Block\Traits\ValidateBlockRequestTrait;
use Concrete\Core\Block\Command\AddBlockToPageCommand;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Block\Command\SortAreaBlocksCommand;
use Concrete\Core\Block\Command\UpdatePageBlockCommand;
use Concrete\Core\Api\Fractal\Transformer\BaseBlockTransformer;
use Concrete\Core\Api\Fractal\Transformer\CollectionVersionTransformer;
use Concrete\Core\Api\Resources;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class Areas extends ApiController implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;
    use GetBlockToEditTrait;
    use ValidateBlockRequestTrait;

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}",
     *     tags={"areas"},
     *     operationId="addBlockToPageArea",
     *     summary="Adds a block to a page area.",
     *     security={
     *         {"authorization": {"pages:areas:add_block"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="areaHandle",
     *         in="path",
     *         description="Area Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/NewBlock"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Block"),
     *     ),
     * )
     */
    public function addBlock($pageID, $areaHandle)
    {
        $content = json_decode($this->request->getContent(), true);

        $page = Page::getByID($pageID);
        if ($page && $page->isError() && $page->getError() == COLLECTION_NOT_FOUND) {
            return $this->error(t('Page not found.', 404));
        }
        $area = Area::getOrCreate($page, $areaHandle);
        $blockType = BlockType::getByHandle($content['type']);
        if (!$blockType) {
            return $this->error(t('Invalid block type handle.', 401));
        }
        $checker = new Checker($area);
        if (!$checker->canAddBlock($blockType)) {
            return $this->error(t('You do not have permission to add this block type to this area on this page.', 403));
        }

        $beforeBlock = null;
        if (isset($content['before_block']) && $content['before_block'] !== null && $content['before_block'] !== '') {
            $beforeBlockID = filter_var($content['before_block'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($beforeBlockID === false) {
                return $this->error(t('Invalid block ID.'), 400);
            }
            try {
                list(, $beforeBlock) = $this->getBlockToWorkWith($page, $areaHandle, $beforeBlockID);
            } catch (AreaNotFoundException $e) {
                return $this->error(t('Area not found.'), 404);
            } catch (BlockNotFoundException $e) {
                return $this->error(t('The block to place the new block before could not be found in this area.'), 404);
            }
        }

        $data = $blockType->getController()->getImportDataFromApiValue($page, (array) $content['value']);

        $command = new AddBlockToPageCommand();
        $command->setPage($page);
        $command->setArea($area);
        $command->setBlockType($blockType);
        $command->setData($data);
        $command->setBeforeBlock($beforeBlock);
        // the received data is in the CIF format (that's how we export blocks)
        $command->setSaveMode(SaveMode::SAVE_MODE_IMPORT);

        $block = $this->app->executeCommand($command);

        $transformer = new BaseBlockTransformer();
        $transformer->setDefaultIncludes(['page']);
        return $this->transform($block, $transformer, Resources::RESOURCE_BLOCKS);
    }

    /**
     * @OA\Put(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}/sort",
     *     tags={"areas"},
     *     operationId="sortBlocksInPageArea",
     *     summary="Sorts the blocks of a page area.",
     *     security={
     *         {"authorization": {"pages:areas:sort_blocks"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="areaHandle",
     *         in="path",
     *         description="Area Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/SortedAreaBlocks"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/SortedAreaBlocksResponse"),
     *     ),
     * )
     */
    public function sortBlocks($pageID, $areaHandle)
    {
        $content = json_decode($this->request->getContent(), true);

        $page = Page::getByID($pageID, 'RECENT');
        if (!$page || ($page->isError() && $page->getError() == COLLECTION_NOT_FOUND)) {
            return $this->error(t('Page not found.'), 404);
        }
        $area = Area::get($page, $areaHandle);
        if (!is_object($area)) {
            return $this->error(t('Area not found.'), 404);
        }
        $checker = new Checker($area);
        if (!$checker->canEditAreaContents()) {
            return $this->error(t('You do not have permission to sort the blocks of this area on this page.'), 403);
        }

        if (!is_array($content['blocks'] ?? null)) {
            return $this->error(t('Missing the list of the IDs of the blocks of the area.'), 400);
        }
        $blockIDs = [];
        foreach ($content['blocks'] as $blockID) {
            $blockID = filter_var($blockID, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($blockID === false) {
                return $this->error(t('Invalid block ID.'), 400);
            }
            if (in_array($blockID, $blockIDs, true)) {
                return $this->error(t('Duplicated block ID: %s', $blockID), 400);
            }
            $blockIDs[] = $blockID;
        }

        $command = new SortAreaBlocksCommand();
        $command
            ->setPage($page)
            ->setArea($area)
            ->setBlockIDs($blockIDs)
        ;

        try {
            $sortedCollection = $this->app->executeCommand($command);
        } catch (HandlerFailedException $x) {
            $error = $x->getPrevious();
            if ($error instanceof \InvalidArgumentException) {
                return $this->error($error->getMessage(), 400);
            }
            if ($error instanceof BlockNotFoundException) {
                return $this->error(t('Block not found.'), 404);
            }
            throw $x;
        }

        $transformer = new CollectionVersionTransformer();
        $version = $transformer->transform($sortedCollection->getVersionObject());

        return new JsonResponse([
            'area' => $areaHandle,
            'object' => Resources::RESOURCE_BLOCKS,
            'blocks' => $blockIDs,
            'version' => $version,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}/{blockID}",
     *     tags={"areas"},
     *     operationId="deleteBlockFromPageArea",
     *     summary="Deletes a block from a page area.",
     *     security={
     *         {"authorization": {"pages:areas:delete_block"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="areaHandle",
     *         in="path",
     *         description="Area Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="blockID",
     *         in="path",
     *         description="ID of block",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeletedAreaBlockResponse"),
     *     ),
     * )
     */
    public function deleteBlock($pageID, $areaHandle, $blockID)
    {
        $page = Page::getByID($pageID, 'RECENT');
        if ($page && $page->isError() && $page->getError() == COLLECTION_NOT_FOUND) {
            return $this->error(t('Page not found.', 404));
        }

        try {
            list($area, $b) = $this->getBlockToWorkWith($page, $areaHandle, $blockID);
        } catch (AreaNotFoundException $e) {
            return $this->error(t('Area not found.', 404));
        } catch (BlockNotFoundException $e) {
            return $this->error(t('Block not found.', 404));
        }

        $checker = new Checker($b);
        if (!$checker->canDeleteBlock()) {
            return $this->error(t('You do not have permission to delete this block on this page.', 403));
        }

        $blockToEdit = $this->getBlockToEdit($page, $area, $areaHandle, $blockID);
        $blockToEditPage = $blockToEdit->getBlockCollectionObject();
        $command = new DeleteBlockCommand(
            $blockToEdit->getBlockID(),
            $blockToEditPage->getCollectionID(),
            $blockToEditPage->getVersionID(),
            $areaHandle
        );

        $this->app->executeCommand($command);

        $transformer = new CollectionVersionTransformer();
        $version = $transformer->transform($blockToEditPage->getVersionObject());
        return new JsonResponse([
            'id' => $blockID,
            'object' => Resources::RESOURCE_BLOCKS,
            'deleted' => true,
            'version' => $version,
        ]);

    }

    /**
     * @OA\Put(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}/{blockID}",
     *     tags={"areas"},
     *     operationId="updateBlockInPageArea",
     *     summary="Updates a block within a page area.",
     *     security={
     *         {"authorization": {"pages:areas:update_block"}}
     *     },
     *     @OA\Parameter(
     *         name="pageID",
     *         in="path",
     *         description="ID of page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="areaHandle",
     *         in="path",
     *         description="Area Name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="blockID",
     *         in="path",
     *         description="ID of block",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdatedBlock"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeletedAreaBlockResponse"),
     *     ),
     * )
     */
    public function updateBlock($pageID, $areaHandle, $blockID)
    {
        $content = json_decode($this->request->getContent(), true);

        $page = Page::getByID($pageID, 'RECENT');
        if ($page && $page->isError() && $page->getError() == COLLECTION_NOT_FOUND) {
            return $this->error(t('Page not found.', 404));
        }

        try {
            list($area, $b) = $this->getBlockToWorkWith($page, $areaHandle, $blockID);
        } catch (AreaNotFoundException $e) {
            return $this->error(t('Area not found.', 404));
        } catch (BlockNotFoundException $e) {
            return $this->error(t('Block not found.', 404));
        }

        $checker = new Checker($b);
        if (!$checker->canEditBlock()) {
            return $this->error(t('You do not have permission to edit this block on this page.', 403));
        }

        $body = $b->getController()->getImportDataFromApiValue($page, (array) $content['value']);
        $r = $this->validateBlock($b, $body);
        if ($r instanceof JsonResponse) {
            return $r;
        }

        $blockToEdit = $this->getBlockToEdit($page, $area, $areaHandle, $blockID);

        $command = new UpdatePageBlockCommand();
        $command->setPage($page);
        $command->setData($body);
        $command->setBlock($blockToEdit);
        // the received data is in the CIF format (that's how we export blocks)
        $command->setSaveMode(SaveMode::SAVE_MODE_IMPORT);

        $block = $this->app->executeCommand($command);

        $transformer = new BaseBlockTransformer();
        $transformer->setDefaultIncludes(['page']);

        return $this->transform($block, $transformer, Resources::RESOURCE_BLOCKS);
    }


}

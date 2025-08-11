<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Area\Exception\AreaNotFoundException;
use Concrete\Core\Block\Exception\BlockNotFoundException;
use Concrete\Core\Block\Traits\GetBlockToEditTrait;
use Concrete\Core\Block\Traits\ValidateBlockRequestTrait;
use Concrete\Core\Block\Command\AddBlockToPageCommand;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Block\Command\UpdatePageBlockCommand;
use Concrete\Core\Api\Fractal\Transformer\BaseBlockTransformer;
use Concrete\Core\Api\Fractal\Transformer\CollectionVersionTransformer;
use Concrete\Core\Api\Resources;
use Symfony\Component\HttpFoundation\JsonResponse;

class Areas extends ApiController implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;
    use GetBlockToEditTrait;
    use ValidateBlockRequestTrait;

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}",
     *     tags={"areas"},
     *     summary="Adds a block to a page area.",
     *     security={
     *         {"authorization": {"pages:areas:add_blocks"}}
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

        $command = new AddBlockToPageCommand();
        $command->setPage($page);
        $command->setArea($area);
        $command->setBlockType($blockType);
        $command->setData($content['value']);

        $block = $this->app->executeCommand($command);

        $transformer = new BaseBlockTransformer();
        $transformer->setDefaultIncludes(['page']);
        return $this->transform($block, $transformer, Resources::RESOURCE_BLOCKS);
    }

    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/pages/{pageID}/{areaHandle}/{blockID}",
     *     tags={"areas"},
     *     summary="Deletes a block from a page area.",
     *     security={
     *         {"authorization": {"pages:areas:delete_blocks"}}
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
     *     summary="Updates a block within a page area.",
     *     security={
     *         {"authorization": {"pages:areas:update_blocks"}}
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

        $body = (array) $content['value'];
        $r = $this->validateBlock($b, $body);
        if ($r instanceof JsonResponse) {
            return $r;
        }

        $blockToEdit = $this->getBlockToEdit($page, $area, $areaHandle, $blockID);

        $command = new UpdatePageBlockCommand();
        $command->setPage($page);
        $command->setData($content['value']);
        $command->setBlock($blockToEdit);

        $block = $this->app->executeCommand($command);

        $blockToEdit->update($body);
        $transformer = new BaseBlockTransformer();
        $transformer->setDefaultIncludes(['page']);

        return $this->transform($block, $transformer, Resources::RESOURCE_BLOCKS);
    }


}

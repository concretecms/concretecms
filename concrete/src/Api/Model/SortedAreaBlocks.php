<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Model;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @OA\Schema(
 *     title="SortedAreaBlocks model",
 *     description="The blocks of a page area, in the wanted display order"
 * )
 */
class SortedAreaBlocks
{
    /**
     * @OA\Property(
     *     type="array",
     *     title="Block IDs",
     *     description="The IDs of the blocks of the area, in the wanted display order: they must be all and only the IDs of the blocks currently in the area.",
     *     @OA\Items(type="integer", format="int64")
     * )
     *
     * @var int[]
     */
    private $blocks;
}

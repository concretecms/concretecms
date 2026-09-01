<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Model;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @OA\RequestBody(
 *     request="SortedAreaBlocks",
 *     description="The IDs of the blocks of a page area, in the wanted display order",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/SortedAreaBlocks"),
 * )
 */
class SortedAreaBlocksRequestBody
{
}

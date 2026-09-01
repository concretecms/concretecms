<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Response;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @OA\Schema(
 *     title="SortedAreaBlocksResponse model",
 * )
 */
class SortedAreaBlocksResponse
{
    /**
     * @OA\Property(type="string", title="Area handle")
     *
     * @var string
     */
    private $area;

    /**
     * @OA\Property(type="string", title="Object type")
     *
     * @var string
     */
    private $object;

    /**
     * @OA\Property(
     *     type="array",
     *     title="Block IDs",
     *     description="The IDs of the blocks of the area, in their new display order.",
     *     @OA\Items(type="integer", format="int64")
     * )
     *
     * @var int[]
     */
    private $blocks;

    /**
     * @OA\Property(title="Version", ref="#/components/schemas/PageVersion")
     *
     * @var string
     */
    private $version;
}

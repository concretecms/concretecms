<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="NewBlock model",
 *     description="A Concrete Block"
 * )
 */
class NewBlock
{

    /**
     * @OA\Property(type="string", title="Block Type Handle")
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(type="object", title="Block value", description="Key/Value object that maps to the request array that powers the block editing interface.")
     *
     * @var string
     */
    private $value;

    /**
     * @OA\Property(type="integer", format="int64", title="Insert before block", description="The ID of an already existing block of the same area: the new block will be placed just before it. If not specified, the new block will be appended at the end of the area.")
     *
     * @var int|null
     */
    private $before_block;



}

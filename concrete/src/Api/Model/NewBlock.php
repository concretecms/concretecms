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



}

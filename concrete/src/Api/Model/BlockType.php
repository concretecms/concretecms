<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="BlockType model",
 *     description="A Concrete Block Type"
 * )
 */
class BlockType
{
    /**
     * @OA\Property(type="string", title="Block Type Handle")
     *
     * @var string
     */
    private $handle;

    /**
     * @OA\Property(type="string", title="Block Type Name")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(type="string", title="Block Type Description")
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(type="object", title="Block value schema", description="The JSON Schema of the value accepted when adding or updating a block of this type.")
     *
     * @var array
     */
    private $value_schema;
}

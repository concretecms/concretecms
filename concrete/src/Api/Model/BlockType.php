<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Model;

defined('C5_EXECUTE') or die('Access Denied.');

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
     * @OA\Property(type="string", nullable=true, title="Package Handle", description="The handle of the package providing the block type (NULL for the block types of the core).")
     *
     * @var string|null
     */
    private $package;

    /**
     * @OA\Property(type="object", title="Block value schema", description="The JSON Schema of the value accepted when adding or updating a block of this type.")
     *
     * @var array
     */
    private $value_schema;
}

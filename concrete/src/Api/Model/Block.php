<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="Block model",
 * )
 */
class Block
{

    /**
     * @OA\Property(type="integer", format="int64", title="Block ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", format="string", title="Block Type Handle")
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(type="object", title="Block value")
     *
     * @var string
     */
    private $value;



}
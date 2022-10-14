<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="Custom Attribute",
 * )
 */
class CustomAttribute
{

    /**
     * @OA\Property(type="integer", format="int64", title="Attribute Value ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", format="string", title="Attribute Type handle")
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(type="string", format="string", title="Attribute Key Handle")
     *
     * @var string
     */
    private $key;

    /**
     * @OA\Property(type="object", title="Data")
     *
     * @var string
     */
    private $value;


}
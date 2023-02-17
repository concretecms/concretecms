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
     * @OA\Property(
     *     oneOf={
     *         @OA\Schema(type="string"),
     *         @OA\Schema(type="integer"),
     *         @OA\Schema(type="boolean"),
     *         @OA\Schema(type="object")
     * })
     *
     * @var string
     */
    private $value;


}
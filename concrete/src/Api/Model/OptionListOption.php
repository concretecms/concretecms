<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="OptionList Option Value",
 * )
 */
class OptionListOption
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", title="Option Value")
     *
     * @var string
     */
    private $value;


    /**
     * @OA\Property(type="string", title="Option Value (Plain Text)")
     *
     * @var string
     */
    private $display_value;


}
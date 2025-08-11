<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="NewPage model",
 *     description="A Concrete User",
 *     allOf={@OA\Schema(ref="#/components/schemas/UpdatedPage")}
 * )
 */
class NewPage
{


    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $parent;




}

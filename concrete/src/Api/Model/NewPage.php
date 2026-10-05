<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="NewPage model",
 *     description="A Concrete Page",
 *     required={"name", "parent", "type", "template"},
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

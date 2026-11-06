<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="NewGroup model",
 *     description="A Concrete User Group",
 *     required={"name"}
 * )
 */
class NewGroup
{

    /**
     * @OA\Property(type="string", title="Group Name")
     *
     * @var string
     */
    private $name;


}

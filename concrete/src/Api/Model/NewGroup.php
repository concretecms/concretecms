<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="NewGroup model", description="A Concrete User Group")
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

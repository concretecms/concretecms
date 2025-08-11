<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     schema="Group",
 *     type="object",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/NewGroup"),
 *     }
 * )
 */

class Group
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;


}

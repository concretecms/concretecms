<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="CalendarEventVersion")
 */
class CalendarEventVersion
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     format="boolean",
     *     title="Is Approved",
     * )
     *
     * @var string
     */
    private $is_approved;


}
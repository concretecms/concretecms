<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="CalendarEvent model",
 * )
 */
class CalendarEvent
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", format="string", title="Block Area")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(title="Page Version", ref="#/components/schemas/CalendarEventVersion")
     *
     * @var string
     */
    private $version;

    /**
     * @OA\Property(type="array", title="Custom Attributes", @OA\Items(ref="#/components/schemas/CustomAttribute"))
     *
     * @var string
     */
    private $custom_attributes;


}
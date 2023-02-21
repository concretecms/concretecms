<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="Calendar model",
 * )
 */
class Calendar
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
     * @OA\Property(title="Site", ref="#/components/schemas/Site")
     *
     * @var string
     */
    private $site;




}
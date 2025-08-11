<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="PageVersion")
 */
class PageVersion
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

    /**
     * @OA\Property(type="date", title="Date page created")
     *
     * @var string
     */
    private $date_created;

    /**
     * @OA\Property(type="date", title="Date approved")
     *
     * @var string
     */
    private $date_approved;

    /**
     * @OA\Property(type="date", title="Publish end date")
     *
     * @var string
     */
    private $publish_end_date;


}
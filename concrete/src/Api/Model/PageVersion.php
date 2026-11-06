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
     * @OA\Property(
     *     format="boolean",
     *     title="Is New Draft",
     *     description="Whether this version is an unapproved draft (cvIsNew).",
     * )
     *
     * @var bool
     */
    private $is_new;

    /**
     * @OA\Property(type="string", title="Version Comments")
     *
     * @var string
     */
    private $comments;

    /**
     * @OA\Property(type="date", title="Version creation date")
     *
     * @var string
     */
    private $date_created;

    /**
     * @OA\Property(type="date", title="Version approval date")
     *
     * @var string
     */
    private $date_approved;

    /**
     * @OA\Property(type="date", title="Version publish end date")
     *
     * @var string
     */
    private $publish_end_date;


}
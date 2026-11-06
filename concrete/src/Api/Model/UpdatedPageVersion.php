<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="UpdatedPageVersion model",
 *     description="A Concrete Page Version"
*     )
 */
class UpdatedPageVersion
{

    /**
     * @OA\Property(type="boolean", title="Is Version Approved")
     *
     * @var boolean
     */
    private $is_approved;

    /**
     * @OA\Property(type="date", title="Version publish end date")
     *
     * @var string
     */
    private $publish_end_date;


}

<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="NewPageVersion model",
 *     description="Optional payload when creating an editable page version draft"
 * )
 */
class NewPageVersion
{

    /**
     * @OA\Property(type="string", title="Version Comments", description="Comments stored on the cloned draft version. Ignored when a RECENT draft already exists.")
     *
     * @var string
     */
    private $comments;

}

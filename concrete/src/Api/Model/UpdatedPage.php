<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="UpdatedPage model",
 *     description="A Concrete Page"
*     )
 */
class UpdatedPage
{

    /**
     * @OA\Property(type="string", title="ID")
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(type="string", title="Short description")
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(type="string", title="Page Type", description="The handle of the page type you want to apply to this page.")
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(type="string", title="Page Type", description="The handle of the page template you want to apply to this page.")
     *
     * @var string
     */
    private $template;


}

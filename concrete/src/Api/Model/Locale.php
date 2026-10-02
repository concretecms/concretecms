<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="Locale model", description="A Concrete Site Locale object.")
 */
class Locale
{

    /**
     * @OA\Property(type="integer", title="ID")
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(type="string", title="Country code")
     *
     * @var string
     */
    private $country;

    /**
     * @OA\Property(type="string", title="Language code")
     *
     * @var string
     */
    private $language;

    /**
     * @OA\Property(type="integer", title="Home Page ID")
     *
     * @var string
     */
    private $home_page_id;




}

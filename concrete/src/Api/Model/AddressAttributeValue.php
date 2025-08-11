<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="Address Attribute Value",
 * )
 */
class AddressAttributeValue
{

    /**
     * @OA\Property(type="string", title="Address 1")
     *
     * @var string
     */
    private $address1;

    /**
     * @OA\Property(type="string", title="Address 2")
     *
     * @var string
     */
    private $address2;

    /**
     * @OA\Property(type="string", title="Address 3")
     *
     * @var string
     */
    private $address3;

    /**
     * @OA\Property(type="string", title="City")
     *
     * @var string
     */
    private $city;

    /**
     * @OA\Property(type="string", title="State/Province Code")
     *
     * @var string
     */
    private $state_province;

    /**
     * @OA\Property(type="string", title="Country Code")
     *
     * @var string
     */
    private $country;

    /**
     * @OA\Property(type="string", title="Postal Code")
     *
     * @var string
     */
    private $postal_code;




}
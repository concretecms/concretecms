<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(
 *     title="User model",
 * )
 */
class User
{

    /**
     * @OA\Property(
     *     format="int64",
     *     title="User ID",
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *     format="string",
     *     title="Username",
     * )
     *
     * @var string
     */
    private $username;

    /**
     * @OA\Property(
     *     format="string",
     *     title="Email",
     * )
     *
     * @var string
     */
    private $email;

    /**
     * @OA\Property(
     *     format="date",
     *     title="Date Added",
     * )
     *
     * @var string
     */
    private $date_added;

    /**
     * @OA\Property(
     *     format="date",
     *     title="Date Password Last Changed",
     * )
     *
     * @var string
     */
    private $date_password_last_changed;

    /**
     * @OA\Property(
     *     format="date",
     *     title="Date Last Updated",
     * )
     *
     * @var string
     */
    private $date_last_updated;

    /**
     * @OA\Property(
     *     format="string",
     *     title="Status",
     * )
     *
     * @var string
     */
    private $status;

    /**
     * @OA\Property(
     *     format="int64",
     *     description="Total number of times logged in",
     *     title="Total Logins",
     * )
     *
     * @var string
     */
    private $total_logins;

    /**
     * @OA\Property(
     *     format="boolean",
     *     title="Has Avatar",
     * )
     *
     * @var string
     */
    private $has_avatar;

    /**
     * @OA\Property(
     *     format="string",
     *     description="Fill URL to the user's profile picture",
     *     title="Avatar",
     * )
     *
     * @var string
     */
    private $avatar;

    /**
     * @OA\Property(format="int64", title="Last Login Timestamp")
     *
     * @var int
     */
    private $last_login;

    /**
     * @OA\Property(format="int64", title="Previous Login Timestamp")
     *
     * @var int
     */
    private $previous_login;

    /**
     * @OA\Property(format="string", title="Timezone")
     *
     * @var string
     */
    private $timezone;

    /**
     * @OA\Property(format="string", title="Language")
     *
     * @var string
     */
    private $language;

    /**
     * @OA\Property(type="array", title="Custom Attributes", @OA\Items(ref="#/components/schemas/CustomAttribute"))
     *
     * @var string
     */
    private $custom_attributes;

}
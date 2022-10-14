<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="NewUser model", description="A Concrete User")
 */
class NewUser
{

    /**
     * @OA\Property(type="string", title="Username")
     *
     * @var string
     */
    private $username;

    /**
     * @OA\Property(type="string", title="Email")
     *
     * @var string
     */
    private $email;

    /**
     * @OA\Property(type="string", title="Password")
     *
     * @var string
     */
    private $password;

    /**
     * @OA\Property(type="string", title="Language")
     *
     * @var string
     */
    private $language;


}

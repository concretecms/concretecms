<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="UpdatedUser model", description="A Concrete User")
 */
class UpdatedUser
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
     * @OA\Property(type="string", title="Language")
     *
     * @var string
     */
    private $language;



}

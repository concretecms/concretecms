<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="UserSocialLink model")
 */
class UserSocialLink
{
    
    /**
     * @OA\Property(type="string", title="Service handle")
     */
    private $service;

    /**
     * @OA\Property(type="string", title="Service Info")
     */
    private $service_info;


}

<?php

namespace Concrete\Core\Api\OAuth\Server;

use Concrete\Core\User\UserInfo;
use League\OpenIdConnectClaims\ClaimsSet;

class ClaimsSetFactory
{

    public function createFromUserInfo(UserInfo $user)
    {
        $set = new ClaimsSet();
        $set->setIdentifier($user->getUserID());
        $set->setEmail($user->getUserEmail());
        $set->setEmailVerified($user->isValidated());
        $set->setUsername($user->getUserName());
        $set->setProfileUrl($user->getUserPublicProfileUrl());

        $map = [
            'setFirstName' => 'first_name',
            'setLastName' => 'last_name',
        ];

        // Apply arbitrary attributes
        foreach ($map as $method => $attribute) {
            $value =  $user->getAttribute($attribute);

            if ($value) {
                $set->{$method} = $value;
            }
        }

        return $set;
    }

}

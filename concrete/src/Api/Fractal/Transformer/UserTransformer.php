<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Carbon\Carbon;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'groups',
        'custom_attributes',
    ];

    /**
     * Basic transforming of a user into an array
     *
     * @param UserInfo $user
     * @return array
     */
    public function transform(UserInfo $user)
    {
        return [
            'id' => $user->getUserID(),
            'username' => $user->getUserName(),
            'email' => $user->getUserEmail(),
            'date_added' => Carbon::make($user->getUserDateAdded())->toAtomString(),
            'status' => $this->getUserStatus($user),
            'total_logins' => $user->getNumLogins(),
            'date_password_last_changed' => $user->getUserLastPasswordChange() ?
                Carbon::make($user->getUserLastPasswordChange())->toAtomString() : null,
            'date_last_updated' => $user->getUserDateLastUpdated() ?
                Carbon::make($user->getUserDateLastUpdated())->toAtomString() : null,
            'has_avatar' => $user->hasAvatar(),
            'avatar' => $user->getUserAvatar()->getPath(),
            'last_login' => $user->getLastLogin(),
            'previous_login' => $user->getPreviousLogin(),
            'timezone' => $user->getUserTimezone(),
            'language' => $user->getUserDefaultLanguage(),
        ];
    }

    public function includeGroups(UserInfo $user)
    {
        $groups = $user->getUserObject()->getUserGroupObjects();
        return new Collection($groups, new GroupTransformer(), Resources::RESOURCE_GROUPS);
    }

    public function includeCustomAttributes(UserInfo $user)
    {
        $values = $user->getObjectAttributeCategory()->getAttributeValues($user);
        return new Collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }


    protected function getUserStatus(UserInfo $user)
    {
        if ($user->isActive()) {
            $currentStatus = 'active';
        } elseif ($user->isValidated()) {
            $currentStatus = 'inactive';
        } else {
            $currentStatus = 'unvalidated';
        }
        return $currentStatus;
    }
}

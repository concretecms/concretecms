<?php
namespace Concrete\Core\User;

use Concrete\Core\Localization\Service\Date as DateService;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @var DateService
     */
    protected $date;

    public function __construct(DateService $date)
    {
        $this->date = $date;
    }

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
            'name' => $user->getUserName(),
            'email' => $user->getUserEmail(),
            'dateAdded' => $this->date->formatDateTime($user->getUserDateAdded()),
            'status' => $this->getUserStatus($user),
            'totalLogins' => $user->getNumLogins(),
        ];
    }

    protected function getUserStatus(UserInfo $user)
    {
        if ($user->isActive()) {
            $currentStatus = t('Active');
        } elseif ($user->isValidated()) {
            $currentStatus = t('Inactive');
        } else {
            $currentStatus = t('Unvalidated');
        }

        return $currentStatus;
    }
}

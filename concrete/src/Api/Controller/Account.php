<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\User\User;
use Concrete\Core\Api\Fractal\Transformer\UserTransformer;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Item;

class Account implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/account",
     *     tags={"account"},
     *     security={
     *         {"authorization": {"account:read"}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="The currently logged in User",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */
    public function read()
    {
        if ($this->user->isRegistered()) {
            $info = $this->user->getUserInfoObject();
            return new Item($info, $this->app->make(UserTransformer::class), Resources::RESOURCE_USERS);
        } else {
            throw new \RuntimeException('There is no user associated with request');
        }

    }

}

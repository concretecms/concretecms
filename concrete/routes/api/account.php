<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/api/v1
 * Namespace: <none>
 */

$router->all('/account/info', function () use ($app) {
    $request = $app->make(\Concrete\Core\Http\Request::class);
    $loggedInUser = $request->attributes->get('oauth_user_id');

    if (!$loggedInUser) {
        throw new \RuntimeException('Invalid user associated with request');
    }

    $userRepository = $app->make(\Concrete\Core\User\UserInfoRepository::class);
    $user = $userRepository->getByID($loggedInUser);

    return new \League\Fractal\Resource\Item($user, function (\Concrete\Core\User\UserInfo $user) {
        return [
            'email' => $user->getUserEmail(),
            'firstName' => $user->getAttributeValue('first_name'),
            'lastName' => $user->getAttribute('last_name'),
            'id' => $user->getUserID(),
            'username' => $user->getUserName(),
        ];
    });
})->setScopes('account:read');

<?php
namespace Concrete\Core\Authentication\Type\OAuth\OAuth2;

use Concrete\Core\Authentication\Type\OAuth\GenericOauthTypeController;
use Concrete\Core\Routing\RedirectResponse;
use OAuth\Common\Exception\Exception;
use OAuth\OAuth2\Service\AbstractService;
use User;

abstract class GenericOauth2TypeController extends GenericOauthTypeController
{

    /** @var AbstractService */
    protected $service;

    public function handle_authentication_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string)$url))->send();
        exit;
    }

    public function handle_authentication_callback()
    {
        $user = new User;
        if ($user && !$user->isError() && $user->isLoggedIn()) {
            $this->handle_attach_callback();
        }
        $token = \Request::getInstance()->get('oauth_token');
        $verifier = \Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);

        if ($token) {
            try {
                $user = $this->attemptAuthentication();
                if ($user) {
                    $this->completeAuthentication($user);
                } else {
                    $this->showError(
                        'No local user account associated with this user, please log in with a local account and connect your account from your user profile.');
                }
            } catch (Exception $e) {
                $this->showError($e->getMessage());
            } catch (\Exception $e) {
                $this->showError('An unexpected error occurred.');
            }
        } else {
            $this->showError('Failed to complete authentication.');
        }
        exit;
    }

    public function handle_attach_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string)$url))->send();
        exit;
    }

    public function handle_attach_callback()
    {
        $user = new User();
        if (!$user->isLoggedIn()) {
            id(new RedirectResponse(\URL::to('')))->send();
            exit;
        }

        $token = \Request::getInstance()->get('oauth_token');
        $verifier = \Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);

        if ($token) {
            if ($this->bindUser($user, $this->getExtractor(true)->getUniqueId())) {
                $this->showSuccess('Successfully attached.');
                exit;
            }
        }
        $this->showError('Unable to attach user.');
        exit;
    }

    public function view()
    {
        // Nothing here.
    }

    /**
     * Method used to clean up.
     * This method must be defined, if it isn't needed, leave it blank.
     *
     * @param \User $u
     * @return void
     */
    public function deauthenticate(User $u)
    {
        // Nothing to do here.
    }

    /**
     * Test user authentication status.
     *
     * @param \User $u
     * @return bool Returns true if user is authenticated, false if not
     */
    public function isAuthenticated(User $u)
    {
        return $u->isLoggedIn();
    }

}

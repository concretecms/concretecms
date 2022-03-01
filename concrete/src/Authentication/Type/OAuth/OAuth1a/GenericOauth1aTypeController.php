<?php
namespace Concrete\Core\Authentication\Type\OAuth\OAuth1a;

use Concrete\Core\Authentication\Type\OAuth\GenericOauthTypeController;
use Concrete\Core\Routing\RedirectResponse;
use OAuth\Common\Exception\Exception;
use Concrete\Core\User\User;

abstract class GenericOauth1aTypeController extends GenericOauthTypeController
{
    public function handle_authentication_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_authentication_callback()
    {
        $user = $this->app->make(User::class);
        if ($user && !$user->isError() && $user->isLoggedIn()) {
            // We should NOT allow you to complete the authentication flow and potentially rebind the
            // logged-in user here. Instead we halt the authentication flow.
            $this->showError(t('You are already logged in.'));
            return false;
        }
        $token = \Request::getInstance()->get('oauth_token');
        $verifier = \Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);
        $this->setToken($token);

        if ($token) {
            try {
                $user = $this->attemptAuthentication();
                if ($user) {
                    return $this->completeAuthentication($user)->send();
                } else {
                    $this->showError(
                        t('No local user account associated with this user, please log in with a local account and connect your account from your user profile.'));
                }
            } catch (Exception $e) {
                $this->showError($e->getMessage());
            } catch (\Exception $e) {
                $this->showError(t('An unexpected error occurred.'));
            }
        } else {
            $this->showError(t('Failed to complete authentication.'));
        }
        exit;
    }

    public function handle_attach_attempt()
    {
        $token = $this->getService()->requestRequestToken();
        $url = $this->getService()->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
        id(new RedirectResponse((string) $url))->send();
        exit;
    }

    public function handle_attach_callback()
    {
        $user = $this->app->make(User::class);
        if (!$user->isLoggedIn()) {
            id(new RedirectResponse(\URL::to('')))->send();
            exit;
        }

        $token = \Request::getInstance()->get('oauth_token');
        $verifier = \Request::getInstance()->get('oauth_verifier');

        $token = $this->getService()->requestAccessToken($token, $verifier);

        if ($token) {
            if ($this->bindUser($user, $this->getExtractor(true)->getUniqueId())) {
                $this->showSuccess(t('Successfully attached.'));
                exit;
            }
        }
        $this->showError(t('Unable to attach user.'));
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
     */
    public function deauthenticate(User $u)
    {
        // Nothing to do here.
    }

    /**
     * Test user authentication status.
     *
     * @param \User $u
     *
     * @return bool Returns true if user is authenticated, false if not
     */
    public function isAuthenticated(User $u)
    {
        return $u->isLoggedIn();
    }
}

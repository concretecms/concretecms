<?php
namespace Concrete\Core\Validation\CSRF;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Service\Ajax;

class Token
{
    /**
     * Duration (in seconds) of a token.
     *
     * @var int
     */
    const VALID_HASH_TIME_THRESHOLD = 86400; // 24 hours

    /**
     * The default name of the token parameters.
     *
     * @var string
     */
    const DEFAULT_TOKEN_NAME = 'ccm_token';

    /**
     * Get the error message to be shown to the users when a token is not valid.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $app = Application::getFacadeApplication();
        $request = $app->make(Request::class);
        $ajax = $app->make(Ajax::class);
        if ($ajax->isAjaxRequest($request)) {
            return t("Invalid token. Please reload the page and retry.");
        } else {
            return t("Invalid form token. Please reload this form and submit again.");
        }
    }

    /**
     * Create the HTML code of a token.
     *
     * @param string $action An optional identifier of the token
     * @param bool $return Set to true to return the generated code, false to print it out
     *
     * @return string|void
     */
    public function output($action = '', $return = false)
    {
        $hash = $this->generate($action);
        $token = '<input type="hidden" name="' . static::DEFAULT_TOKEN_NAME . '" value="' . $hash . '" />';
        if (!$return) {
            echo $token;
        } else {
            return $token;
        }
    }

    /**
     * Generates a token for a given action. This is a token in the form of time:hash, where hash is md5(time:userID:action:pepper).
     *
     * @param string $action An optional identifier of the token
     * @param int $time The UNIX timestamp to be used to determine the token expiration
     *
     * @return string
     */
    public function generate($action = '', $time = null)
    {
        $u = new User();
        $uID = $u->getUserID();
        if (!$uID) {
            $uID = 0;
        }
        if (!$time) {
            $time = time();
        }
        $app = Application::getFacadeApplication();
        $config = $app->make('config/database');
        $hash = $time . ':' . md5($time . ':' . $uID . ':' . $action . ':' . $config->get('concrete.security.token.validation'));

        return $hash;
    }

    /**
     * Generate a token and return it as a query string variable (eg 'ccm_token=...').
     *
     * @param string $action
     *
     * @return string
     */
    public function getParameter($action = '')
    {
        $hash = $this->generate($action);

        return static::DEFAULT_TOKEN_NAME . '=' . $hash;
    }

    /**
     * Validate a token against a given action.
     *
     * Basically, we check the passed hash to see if:
     * a. the hash is valid. That means it computes in the time:action:pepper format
     * b. the time included next to the hash is within the threshold.
     *
     * @param string $action The action that should be associated to the token
     * @param string $token The token to be validated (if empty we'll retrieve it from the current request)
     *
     * @return bool
     */
    public function validate($action = '', $token = null)
    {
        if ($token == null) {
            $app = Application::getFacadeApplication();
            $request = $app->make(Request::class);
            $token = $request->request->get(static::DEFAULT_TOKEN_NAME);
            if ($token === null) {
                $token = $request->query->get(static::DEFAULT_TOKEN_NAME);
            }
        }
        if (is_string($token)) {
            $parts = explode(':', $token);
            if ($parts[0] && isset($parts[1])) {
                $time = $parts[0];
                $hash = $parts[1];
                $compHash = $this->generate($action, $time);
                $now = time();

                if (substr($compHash, strpos($compHash, ':') + 1) == $hash) {
                    $diff = $now - $time;
                    //hash is only valid if $diff is less than VALID_HASH_TIME_RECORD
                    return $diff <= static::VALID_HASH_TIME_THRESHOLD;
                }
            }
        }

        return false;
    }
}

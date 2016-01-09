<?php
namespace Concrete\Core\Validation\CSRF;

use Config;
use User;

class Token
{
    const VALID_HASH_TIME_THRESHOLD = 86400; // 24 hours (in seconds)

    /**
     * For localization we can't just store this as a constant, unfortunately.
     */
    public function getErrorMessage()
    {
        return t("Invalid form token. Please reload this form and submit again.");
    }

    /**
     * Prints out a generated token as a hidden form field.
     *
     * @param string $action
     * @param bool   $return
     *
     * @return string
     */
    public function output($action = '', $return = false)
    {
        $hash = $this->generate($action);
        $token = '<input type="hidden" name="ccm_token" value="' . $hash . '" />';
        if (!$return) {
            print $token;
        } else {
            return $token;
        }
    }

    /**
     * Generates a unique token for a given action. This is a token in the form of
     * time:hash, where hash is md5(time:userID:action:pepper).
     *
     * @param string $action
     * @param int    $time
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
        if ($time == null) {
            $time = time();
        }
        $config = \Core::make('config/database');
        $hash = $time . ':' . md5($time . ':' . $uID . ':' . $action . ':' . $config->get('concrete.security.token.validation'));

        return $hash;
    }

    /**
     * Returns a generated token as a query string variable.
     *
     * @param string $action
     *
     * @return string
     */
    public function getParameter($action = '')
    {
        $hash = $this->generate($action);

        return 'ccm_token=' . $hash;
    }

    /**
     * Validates against a given action. Basically, we check the passed hash to see if
     * a. the hash is valid. That means it computes in the time:action:pepper format
     * b. the time included next to the hash is within the threshold.
     *
     * @param string $action
     * @param string $token
     *
     * @return bool
     */
    public function validate($action = '', $token = null)
    {
        if ($token == null) {
            $token = isset($_REQUEST['ccm_token']) ? $_REQUEST['ccm_token'] : '';
        }
        $parts = explode(':', $token);
        if ($parts[0]) {
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

        return false;
    }
}

<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Response\Response as PermissionResponse;
use Concrete\Core\Support\Facade\Application;
use Exception;

class Checker
{
    /**
     * The ID of an error (if not falsy), of false|null if no errors.
     * List of some errors: https://github.com/concrete5/concrete5/blob/8.4.2/concrete/bootstrap/configure.php#L326-L336.
     *
     * @var int|null|false
     */
    public $error;

    /**
     * @var \Concrete\Core\Permission\Response\Response|null
     */
    protected $response;

    /**
     * @param \Concrete\Core\Permission\ObjectInterface|null|false $object
     */
    public function __construct($object = false)
    {
        if ($object) {
            $this->response = PermissionResponse::getResponse($object);
            $r = $this->response->testForErrors();
            if ($r) {
                $this->error = $r;
            }
        }
    }

    /**
     * We take any permissions function run on the permissions class and send it into the category object.
     *
     * @param string $f The method name
     * @param array $a The method arguments
     *
     * @return array|object|int
     */
    public function __call($f, $a)
    {
        if ($this->response) {
            switch (count($a)) {
                case 0:
                    $r = $this->response->{$f}();
                    break;
                case 1:
                    $r = $this->response->{$f}($a[0]);
                    break;
                case 2:
                    $r = $this->response->{$f}($a[0], $a[1]);
                    break;
                default:
                    $r = call_user_func_array([$this->response, $f], $a);
                    break;
            }
        } else {
            $app = Application::getFacadeApplication();
            // handles task permissions
            $permission = $app->make('helper/text')->uncamelcase($f);
            $pk = PermissionKey::getByHandle($permission);
            if (!$pk) {
                throw new Exception(t('Unable to get permission key for %s', $permission));
            }
            switch (count($a)) {
                case 0:
                    $r = $pk->validate();
                    break;
                case 1:
                    $r = $pk->{$f}($a[0]);
                    break;
                case 2:
                    $r = $pk->{$f}($a[0], $a[1]);
                    break;
                default:
                    $r = call_user_func_array([$pk, $f], $a);
                    break;
            }
        }

        if (is_array($r) || is_object($r)) {
            return $r;
        }

        return $r ? 1 : 0;
    }

    /**
     * Checks to see if there is a fatal error with this particular permission call.
     *
     * @return bool
     */
    public function isError()
    {
        return $this->error ? true : false;
    }

    /**
     * Returns the error code if there is one.
     *
     * @return int|null|false
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Legacy.
     *
     * @private
     *
     * @return \Concrete\Core\Permission\ObjectInterface
     */
    public function getOriginalObject()
    {
        return $this->response->getPermissionObject();
    }

    /**
     * @return \Concrete\Core\Permission\Response\Response|null
     */
    public function getResponseObject()
    {
        return $this->response;
    }
}

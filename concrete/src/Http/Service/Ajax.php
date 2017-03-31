<?php
namespace Concrete\Core\Http\Service;

use Exception;
use Concrete\Core\Http\Request;

class Ajax
{
    /**
     * Check if a request is an Ajax call.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isAjaxRequest(Request $request)
    {
        $result = false;
        $requestedWith = $request->server->get('HTTP_X_REQUESTED_WITH');
        if (is_string($requestedWith) && strcasecmp($requestedWith, 'XMLHttpRequest') === 0) {
            $result = true;
        }

        return $result;
    }

    /**
     * Sends a result to the client and ends the execution.
     *
     * @param mixed $result
     *
     * @deprecated You should switch to something like:
     * return \Core::make(\Concrete\Core\Http\ResponseFactoryInterface::class)->json(...)
     */
    public function sendResult($result)
    {
        if (@ob_get_length()) {
            @ob_end_clean();
        }
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json; charset=' . APP_CHARSET, true);
        } else {
            header('Content-Type: text/plain; charset=' . APP_CHARSET, true);
        }
        echo json_encode($result);
        die();
    }

    /**
     * Sends an error to the client and ends the execution.
     *
     * @param string|Exception|\Concrete\Core\Error\Error $result the error to send to the client
     * @param mixed $error
     *
     * @deprecated You should switch to something like:
     * return \Core::make(\Concrete\Core\Http\ResponseFactoryInterface::class)->json(...)
     */
    public function sendError($error)
    {
        if (@ob_get_length()) {
            @ob_end_clean();
        }
        if ($error instanceof \Concrete\Core\Error\ErrorList\ErrorList) {
            $error->outputJSON();
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            header('Content-Type: text/plain; charset=' . APP_CHARSET, true);
            echo($error instanceof Exception) ? $error->getMessage() : $error;
        }
        die();
    }
}

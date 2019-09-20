<?php

namespace Concrete\Core\Error\Handler;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\RequestMediaTypeParser;
use Concrete\Core\Support\Facade\Application;
use Config;
use Exception;
use Throwable;
use Whoops\Exception\Formatter;
use Whoops\Handler\Handler;
use Whoops\Util\Misc;

class JsonErrorHandler extends Handler
{
    public function handle()
    {
        if (!$this->isAjaxRequest()) {
            return Handler::DONE;
        }

        $e = $this->getInspector()->getException();
        $detail = 'message';
        if ($e instanceof UserMessageException) {
            $display = true;
            $canBeLogged = $e->canBeLogged();
        } else {
            $display = (bool) Config::get('concrete.debug.display_errors');
            if ($display === true) {
                $detail = Config::get('concrete.debug.detail');
            }
            $canBeLogged = true;
        }
        if ($display === false) {
            $error = ['message' => t('An error occurred while processing this request.')];
        } else {
            if ($detail !== 'debug') {
                $error = ['message' => $e->getMessage()];
            } else {
                $error = Formatter::formatExceptionAsDataArray(
                    $this->getInspector(),
                    true
                );
            }
        }

        if ($canBeLogged && Config::get('concrete.log.errors')) {
            try {
                $e = $this->getInspector()->getException();
                $l = \Core::make('log/exceptions');
                $l->emergency(
                    sprintf(
                        "Exception Occurred: %s:%d %s (%d)\n",
                        $e->getFile(),
                        $e->getLine(),
                        $e->getMessage(),
                        $e->getCode()
                    ),
                    [$e]
                );
            } catch (\Exception $e) {
            }
        }

        $response = [
            'error' => $error,
            'errors' => [$error['message']],
        ];

        if (Misc::canSendHeaders()) {
            if ($this->clientSupportsJson() === true) {
                header('Content-Type: application/json; charset=' . APP_CHARSET, true);
            } else {
                header('Content-Type: text/plain; charset=' . APP_CHARSET, true);
            }
        }

        echo json_encode($response);

        return Handler::QUIT;
    }

    /**
     * Check, if possible, that this execution was triggered by an AJAX request.
     *
     * @return bool
     */
    private function isAjaxRequest()
    {
        return
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Check if the client supports JSON.
     *
     * @param float $minWeight
     *
     * @return bool|null true: yes; false: no; NULL: we weren't able to detect it
     */
    private function clientSupportsJson($minWeight = null)
    {
        try {
            $app = Application::getFacadeApplication();
            $rmrp = $app->make(RequestMediaTypeParser::class);

            return $rmrp->isMediaTypeSupported('application/json', $minWeight);
        } catch (Exception $x) {
            return null;
        } catch (Throwable $x) {
            return null;
        }
    }
}

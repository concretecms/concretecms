<?php
namespace Concrete\Core\Error\Handler;

use Whoops\Handler\JsonResponseHandler;

class JsonErrorHandler extends JsonResponseHandler {

    public function onlyForAjaxRequests($onlyForAjaxRequests = null)
    {
        return true;
    }

}

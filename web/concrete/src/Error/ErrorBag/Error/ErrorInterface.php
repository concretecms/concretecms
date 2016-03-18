<?php
namespace Concrete\Core\Error\ErrorBag\Error;

interface ErrorInterface extends \JsonSerializable
{

    function getMessage();

}

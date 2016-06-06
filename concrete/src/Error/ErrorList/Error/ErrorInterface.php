<?php
namespace Concrete\Core\Error\ErrorList\Error;

interface ErrorInterface extends \JsonSerializable
{

    function getMessage();

}

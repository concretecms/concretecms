<?php
namespace Concrete\Core\Command\Task\Runner\Response;

defined('C5_EXECUTE') or die("Access Denied.");

interface ResponseInterface
{

    public function getMessage(): string;


}

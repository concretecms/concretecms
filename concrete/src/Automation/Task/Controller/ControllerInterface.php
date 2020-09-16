<?php
namespace Concrete\Core\Automation\Task\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName() : string;

    public function getDescription() : string;

    public function getHelpText() : string;

}

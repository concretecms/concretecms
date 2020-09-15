<?php
namespace Concrete\Core\Automation\Command\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

interface ControllerInterface
{

    public function getName() : string;

    public function getDescription() : string;

    public function getHelpText() : string;

}

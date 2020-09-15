<?php
namespace Concrete\Core\Automation\Command\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractController implements ControllerInterface
{

    public function getHelpText(): string
    {
        return '';
    }

}

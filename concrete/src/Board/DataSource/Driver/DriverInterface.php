<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    public function getIconFormatter() : IconFormatterInterface;


}

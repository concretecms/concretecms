<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\Item\Populator\PopulatorInterface;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Filesystem\Element;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{
    
    public function getIconFormatter() : IconFormatterInterface;
    
    public function getConfigurationFormElement() : Element;

    public function getSaver() : SaverInterface;
    
    public function getItemPopulator() : PopulatorInterface;
}

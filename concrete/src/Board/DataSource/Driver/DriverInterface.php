<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Board\Instance\Slot\Content\Populator\PopulatorInterface as ContentPopulatorInterface;
use Concrete\Core\Board\Instance\Item\Populator\PopulatorInterface as ItemPopulatorInterface;
use Concrete\Core\Board\Instance\Item\Filterer\FiltererInterface as ItemFiltererInterface;
use Concrete\Core\Filesystem\Element;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{

    public function getIconFormatter() : IconFormatterInterface;

    public function getConfigurationFormElement() : Element;

    public function getSaver() : SaverInterface;

    public function getItemPopulator() : ItemPopulatorInterface;

    public function getItemFilterer() : ?ItemFiltererInterface;

    public function getContentPopulator() : ContentPopulatorInterface;
}

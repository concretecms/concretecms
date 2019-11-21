<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\BasicIconFormatter;
use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\DataSource\Populator\PagePopulator;
use Concrete\Core\Board\DataSource\Populator\PopulatorInterface;
use Concrete\Core\Board\DataSource\Saver\PageSaver;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Page\PageList;

defined('C5_EXECUTE') or die("Access Denied.");

class PageDriver extends AbstractDriver
{
    
    public function getIconFormatter(): IconFormatterInterface
    {
        return new BasicIconFormatter('fas fa-file');
    }
    
    public function getConfigurationFormElement(): Element
    {
        return new Element('dashboard/boards/configuration/page');
    }
    
    public function getSaver(): SaverInterface
    {
        return $this->app->make(PageSaver::class);
    }
    
    public function getBoardPopulator(): PopulatorInterface
    {
        return $this->app->make(PagePopulator::class);
    }

}

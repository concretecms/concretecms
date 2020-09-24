<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\UserInterface\Icon\BasicIconFormatter;
use Concrete\Core\Application\UserInterface\Icon\IconFormatterInterface;
use Concrete\Core\Board\DataSource\Saver\PageSaver;
use Concrete\Core\Board\DataSource\Saver\SaverInterface;
use Concrete\Core\Board\Instance\Item\Populator\PagePopulator as PageItemPopulator;
use Concrete\Core\Board\Instance\Item\Filterer\FiltererInterface as ItemFiltererInterface;
use Concrete\Core\Board\Instance\Slot\Content\Populator\PagePopulator as PageContentPopulator;
use Concrete\Core\Board\Instance\Item\Populator\PopulatorInterface as ItemPopulatorInterface;
use Concrete\Core\Board\Instance\Slot\Content\Populator\PopulatorInterface as ContentPopulatorInterface;

use Concrete\Core\Filesystem\Element;

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

    public function getItemPopulator(): ItemPopulatorInterface
    {
        return $this->app->make(PageItemPopulator::class);
    }

    public function getContentPopulator(): ContentPopulatorInterface
    {
        return $this->app->make(PageContentPopulator::class);
    }

    public function getItemFilterer(): ?ItemFiltererInterface
    {
        return null;
    }


}

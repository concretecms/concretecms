<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Entity\Page\Container\InstanceArea;

final class Version20191029175713 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/pages/containers');
        $this->createSinglePage('/dashboard/pages/containers/add', 'Add Container', 
            ['exclude_nav' => true]
        );
        $this->createSinglePage('/dashboard/boards');
        $this->createSinglePage('/dashboard/boards/boards', 'View Boards');
        $this->createSinglePage('/dashboard/boards/add', 'Add Board',
            ['exclude_nav' => true]
        );
        $this->createSinglePage('/dashboard/boards/details', 'Board Details',
            ['exclude_nav' => true]
        );
        $this->createSinglePage('/dashboard/boards/data_sources', 'Data Sources',
            ['exclude_nav' => true]
        );
        $this->refreshEntities([
            Board::class,
            DataSource::class,
            ConfiguredDataSource::class,
            Container::class,
            Instance::class,
            InstanceArea::class,
            Template::class,
            Category::class,
            CustomPageTemplateCollection::class,
        ]);
        $bt = BlockType::getByHandle('core_container');
        if (!$bt) {
            BlockType::installBlockType('core_container');
        } else {
            $bt->refresh();
        }
    }
}

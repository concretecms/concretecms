<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\Routine\AddPageDraftsBooleanTrait;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Page\Page;
use SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Attribute\Category\PageCategory;

class Version20170202000000 extends AbstractMigration
{

    use AddPageDraftsBooleanTrait;

    public function up(Schema $schema)
    {
        $this->addColumnIfMissing($schema);
        $app = Application::getFacadeApplication();

        $this->refreshEntities([
            DateTimeSettings::class,
        ]);
        $config = $app->make('config');
        if (!$config->get('app.curl.verifyPeer')) {
            $config->save('app.http_client.sslverifypeer', false);
        }
    }

    public function postUp(Schema $schema)
    {
        $this->migrateDrafts();
        $app = Application::getFacadeApplication();
        $pageAttributeCategory = $app->make(PageCategory::class);
        /* @var PageCategory $pageAttributeCategory */
        $availableAttributes = [];
        foreach ([
                     'exclude_nav',
                     'meta_keywords',
                 ] as $akHandle) {
            $availableAttributes[$akHandle] = $pageAttributeCategory->getAttributeKeyByHandle($akHandle) ? true : false;
        }

        $sp = Page::getByPath('/dashboard/system/files/thumbnails/options');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/files/thumbnails/options');
            $sp->update(['cName' => 'Thumbnail Options']);
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
            if ($availableAttributes['meta_keywords']) {
                $sp->setAttribute('meta_keywords', 'thumbnail, format, png, jpg, jpeg, quality, compression, gd, imagick, imagemagick, transparency');
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}

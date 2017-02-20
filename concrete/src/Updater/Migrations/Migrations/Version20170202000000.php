<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings;
use Concrete\Core\Entity\Attribute\Key\Settings\ExpressSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\ImageFileSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\SelectSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\TextSettings;
use Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings;

class Version20170202000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $app = Application::getFacadeApplication();
        $sp = Page::getByPath('/dashboard/system/files/thumbnails/options');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/files/thumbnails/options');
            $sp->update(['cName' => 'Thumbnail Options']);
            $sp->setAttribute('exclude_nav', true);
            $sp->setAttribute('meta_keywords', 'thumbnail, format, png, jpg, jpeg, quality, compression, gd, imagick, imagemagick, transparency');
        }
        $this->refreshEntities([
            Key::class,
            AddressSettings::class,
            BooleanSettings::class,
            DateTimeSettings::class,
            EmptySettings::class,
            ExpressSettings::class,
            ImageFileSettings::class,
            SelectSettings::class,
            TextareaSettings::class,
            TextSettings::class,
            TopicsSettings::class,
        ]);
        $config = $app->make('config');
        if (!$config->get('app.curl.verifyPeer')) {
            $config->save('app.http_client.sslverifypeer', false);
        }
    }

    public function down(Schema $schema)
    {
    }
}

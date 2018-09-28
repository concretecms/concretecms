<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\Routine\AddPageDraftsBooleanTrait;
use Doctrine\DBAL\Schema\Schema;

class Version20170202000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    use AddPageDraftsBooleanTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $this->addColumnIfMissing($schema);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $app = Application::getFacadeApplication();

        $this->refreshEntities([
            DateTimeSettings::class,
        ]);
        $config = $app->make('config');
        if (!$config->get('app.curl.verifyPeer')) {
            $config->save('app.http_client.sslverifypeer', false);
        }
        $this->migrateDrafts();

        $this->createSinglePage('/dashboard/system/files/thumbnails/options', 'Thumbnail Options', ['exclude_nav' => true, 'meta_keywords' => 'thumbnail, format, png, jpg, jpeg, quality, compression, gd, imagick, imagemagick, transparency']);
    }
}

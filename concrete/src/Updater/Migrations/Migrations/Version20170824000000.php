<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\Routine\AddPageDraftsBooleanTrait;
use Doctrine\DBAL\Schema\Schema;

class Version20170824000000 extends AbstractMigration implements RepeatableMigrationInterface
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
        $this->refreshEntities([
            AddressSettings::class,
        ]);
        $this->migrateDrafts();
        $app = Application::getFacadeApplication();
        // I think this has to be in postUp because it's a completely new table that's not in Schema at all? Bleh.
        $this->refreshEntities([
            Geolocator::class,
        ]);

        $glService = $app->make(GeolocatorService::class);
        /* @var GeolocatorService $glService */
        if ($glService->getByHandle('geoplugin') === null) {
            $geolocator = Geolocator::create(
                'geoplugin',
                'geoPlugin'
            );
            $geolocator
                ->setGeolocatorConfiguration([
                    'url' => 'http://www.geoplugin.net/json.gp?ip=[[IP]]',
                ])
            ;
            $glService->setCurrent($geolocator);
            $em = $glService->getEntityManager();
            $em->persist($geolocator);
            $em->flush($geolocator);
        }

        $this->createSinglePage('/dashboard/system/environment/geolocation', 'Geolocation', ['meta_keywords' => 'geolocation, ip, address, country, nation, place, locate']);
    }
}

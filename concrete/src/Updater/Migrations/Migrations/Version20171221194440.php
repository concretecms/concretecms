<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171221194440 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

    }

    public function postUp(Schema $schema)
    {
        // Cut and pasting code from an earlier migration to ensure that it runs
        $app = Facade::getFacadeApplication();
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
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}

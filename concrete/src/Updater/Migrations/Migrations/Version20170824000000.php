<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170824000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $app = Application::getFacadeApplication();
        $this->refreshEntities([
            Geolocator::class,
            AddressSettings::class,
        ]);

        $pageAttributeCategory = Application::getFacadeApplication()->make(PageCategory::class);
        /* @var PageCategory $pageAttributeCategory */
        $availableAttributes = [];
        foreach (['meta_keywords'] as $akHandle) {
            $availableAttributes[$akHandle] = $pageAttributeCategory->getAttributeKeyByHandle($akHandle) ? true : false;
        }

        $page = Page::getByPath('/dashboard/system/environment/geolocation');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/environment/geolocation');
            $sp->update(['cName' => 'Geolocation']);
            if ($availableAttributes['meta_keywords']) {
                $sp->setAttribute('meta_keywords', 'geolocation, ip, address, country, nation, place, locate');
            }
        }

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

    public function down(Schema $schema)
    {
    }
}

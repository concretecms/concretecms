<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Support\Facade\Application;

class ImportGeolocatorsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'geolocators';
    }

    protected function unserializeOption($value)
    {
        $result = @json_decode($value, true);
        if ($result === null && trim(strtolower($value)) !== 'null') {
            $result = $value;
        }

        return $result;
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->geolocators) && !empty($sx->geolocators->geolocator)) {
            $app = Application::getFacadeApplication();
            $service = $app->make(GeolocatorService::class);
            $em = $service->getEntityManager();
            foreach ($sx->geolocators->geolocator as $xGeolocator) {
                $handle = (string) $xGeolocator['handle'];
                if ($service->getByHandle($handle) === null) {
                    $package = empty($xGeolocator['package']) ? null : static::getPackageObject($xGeolocator['package']);
                    $geolocator = Geolocator::create($handle, $xGeolocator['name'], $package);
                    if (isset($xGeolocator['description'])) {
                        $geolocator->setGeolocatorDescription($xGeolocator['description']);
                    }
                    if (!empty($xGeolocator->option)) {
                        $configuration = [];
                        foreach ($xGeolocator->option as $xOption) {
                            $configuration[(string) $xOption['name']] = $this->unserializeOption((string) $xOption);
                        }
                        $geolocator->setGeolocatorConfiguration($configuration);
                    }
                    if (!empty($xGeolocator['active'])) {
                        $service->setCurrent($geolocator);
                    }
                    $em->persist($geolocator);
                    $em->flush($geolocator);
                }
            }
        }
    }
}

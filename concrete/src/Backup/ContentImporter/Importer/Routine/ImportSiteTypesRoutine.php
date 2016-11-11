<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Site\Type;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Site\Type\Service as TypeService;

class ImportSiteTypesRoutine extends AbstractRoutine
{
    protected $typeService;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, TypeService $typeService)
    {
        $this->typeService = $typeService;
        $this->entityManager = $entityManager;
    }

    public function getHandle()
    {
        return 'site_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->sitetypes)) {
            foreach ($sx->sitetypes->sitetype as $type) {
                $pkg = static::getPackageObject($type['package']);
                $handle = (string) $type['handle'];
                $site_type = $this->typeService->getByHandle($handle);
                if (!is_object($site_type)) {
                    $site_type = $this->typeService->import($handle, (string) $type['name'], $pkg);
                }
                $this->entityManager->persist($site_type);
                $this->entityManager->flush();
            }
        }
    }
}

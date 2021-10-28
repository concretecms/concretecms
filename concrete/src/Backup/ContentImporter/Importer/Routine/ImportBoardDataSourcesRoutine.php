<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Board\DataSource\DataSource;

class ImportBoardDataSourcesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'board_data_sources';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->boarddatasources)) {
            foreach ($sx->boarddatasources->datasource as $data) {
                $pkg = static::getPackageObject($data['package']);
                $handle = (string) $data['handle'];
                $name = (string) $data['name'];
                $source = $em->getRepository(DataSource::class)->findOneByHandle($handle);
                if (!$source) {
                    $source = new DataSource();
                    $source->setHandle($handle);
                    $source->setName($name);
                    $source->setPackage($pkg);
                    $em->persist($source);
                }
            }
        }
        $em->flush();
    }
}

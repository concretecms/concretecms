<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Gathering\DataSource\DataSource;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportGatheringDataSourcesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'gathering_data_sources';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringsources)) {
            foreach ($sx->gatheringsources->gatheringsource as $ags) {
                $pkg = static::getPackageObject($ags['package']);
                $source = DataSource::add((string) $ags['handle'], (string) $ags['name'], $pkg);
            }
        }
    }

}

<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Single;
use Concrete\Core\Permission\Category;

class ImportSinglePageStructureRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'single_pages';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->singlepages)) {
            foreach ($sx->singlepages->page as $p) {
                $pkg = static::getPackageObject($p['package']);
                $spl = Single::add($p['path'], $pkg);
                if (is_object($spl)) {
                    if (isset($p['root']) && $p['root'] == true) {
                        $spl->moveToRoot();
                    }
                    if ($p['name']) {
                        $spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
                    }

                    if ($p['custom-path']) {
                        $spl->setCanonicalPagePath((string) $p['custom-path'], false);
                    }
                }
            }
        }
    }
}

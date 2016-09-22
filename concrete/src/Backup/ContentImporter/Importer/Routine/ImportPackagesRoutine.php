<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPackagesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'packages';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->packages)) {
            foreach ($sx->packages->package as $p) {
                $pkg = Package::getClass((string) $p['handle']);
                if (!$pkg->isPackageInstalled()) {
                    $pkg->install();
                }
            }
        }
    }

}

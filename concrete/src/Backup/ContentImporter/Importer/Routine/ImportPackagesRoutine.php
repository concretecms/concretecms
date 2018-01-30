<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Permission\Category;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Validation\BannedWord\BannedWord;
use Concrete\Core\Package\PackageService;

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
                $pkg = Package::getByHandle((string) $p['handle']);
                if (!$pkg) {
                    $pkgClass = Package::getClass((string) $p['handle']);
                    if ($pkgClass) {
                        $app = Facade::getFacadeApplication();
                        $service = $app->make(PackageService::class);
                        $service->install($pkgClass, []);
                    }
                }
            }
        }
    }

}

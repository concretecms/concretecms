<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Permission\Category;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Validation\BannedWord\BannedWord;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Validation\CSRF\Token;

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
                        /** @var Token $token */
                        $token = $app->make(Token::class);

                        $data = [];

                        if (isset($p['full-content-swap'])) {
                            $data["pkgDoFullContentSwap"] = true;
                            // set this token to perform a full content swap when installing starting point packages
                            $data["ccm_token"] = $token->generate("install_options_selected");

                            if (isset($p['content-swap-file'])) {
                                $data["contentSwapFile"] = (string)$p['content-swap-file'];
                            } else {
                                $data["contentSwapFile"] = "content.xml";
                            }
                        }

                        $service->install($pkgClass, $data);
                    }
                }
            }
        }
    }

}

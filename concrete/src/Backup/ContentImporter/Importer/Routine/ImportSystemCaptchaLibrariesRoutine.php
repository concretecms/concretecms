<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Captcha\Library;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportSystemCaptchaLibrariesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'system_captcha_libraries';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->systemcaptcha)) {
            foreach ($sx->systemcaptcha->library as $th) {
                $pkg = static::getPackageObject($th['package']);
                $scl = Library::getByHandle((string) $th['handle']);
                if (!is_object($scl)) {
                    $scl = Library::add($th['handle'], $th['name'], $pkg);
                }
                if ($th['activated'] == '1') {
                    $scl->activate();
                }
            }
        }
    }

}

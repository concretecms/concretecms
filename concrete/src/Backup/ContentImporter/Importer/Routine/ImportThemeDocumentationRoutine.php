<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Theme\Theme;

class ImportThemeDocumentationRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'themes';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->themes)) {
            foreach ($sx->themes->theme as $th) {
                $pThemeHandle = (string) $th['handle'];

                if (((string) $th['activated']) == '1') {
                    $pt = Theme::getByHandle($pThemeHandle);
                    $pt->installThemeDocumentation();
                }
            }
        }
    }

}

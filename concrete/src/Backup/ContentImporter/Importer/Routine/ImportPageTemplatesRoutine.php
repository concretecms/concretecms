<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Page\Template;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPageTemplatesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_templates';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetemplates)) {
            foreach ($sx->pagetemplates->pagetemplate as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $ptt = Template::getByHandle($pt['handle']);
                if (!is_object($ptt)) {
                    $ptt = Template::add(
                        (string) $pt['handle'],
                        (string) $pt['name'],
                        (string) $pt['icon'],
                        $pkg,
                        (string) $pt['internal']
                    );
                }
            }
        }
    }

}

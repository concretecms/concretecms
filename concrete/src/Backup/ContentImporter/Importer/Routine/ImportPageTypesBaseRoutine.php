<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPageTypesBaseRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_types_base';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                Type::import($p);
            }
        }
    }

}
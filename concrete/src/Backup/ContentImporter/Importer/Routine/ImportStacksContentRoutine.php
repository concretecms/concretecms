<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportStacksContentRoutine extends AbstractPageContentRoutine
{
    public function getHandle()
    {
        return 'stacks_content';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->stacks)) {
            foreach ($sx->stacks->stack as $p) {
                $path = (string) $p['path'];
                if ($path) {
                    $stack = Stack::getByPath($path);
                } else {
                    $stack = Stack::getByName((string) $p['name']);
                }
                if (isset($p->area)) {
                    $this->importPageAreas($stack, $p);
                }
            }
        }
    }

}

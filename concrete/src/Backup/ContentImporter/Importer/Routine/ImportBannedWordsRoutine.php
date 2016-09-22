<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportBannedWordsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'banned_words';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->banned_words)) {
            foreach ($sx->banned_words->banned_word as $p) {
                $bw = BannedWord::add(str_rot13($p));
            }
        }
    }

}

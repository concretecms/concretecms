<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Permission\Category;

class ImportConversationFlagTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'conversation_flag_type';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->flag_types)) {
            foreach ($sx->flag_types->flag_type as $p) {
                $bw = FlagType::add((string) $p);
            }
        }
    }

}

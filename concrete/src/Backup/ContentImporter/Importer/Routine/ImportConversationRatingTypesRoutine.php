<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Conversation\Rating\Type;
use Concrete\Core\Permission\Category;

class ImportConversationRatingTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'conversation_rating_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->conversationratingtypes)) {
            foreach ($sx->conversationratingtypes->conversationratingtype as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = Type::add((string) $th['handle'], (string) $th['name'], $th['points'], $pkg);
            }
        }
    }
}

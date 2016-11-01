<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Conversation\Editor\Editor;
use Concrete\Core\Permission\Category;

class ImportConversationEditorsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'groups';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->conversationeditors)) {
            foreach ($sx->conversationeditors->editor as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = Editor::add((string) $th['handle'], (string) $th['name'], $pkg);
                if ($th['activated'] == '1') {
                    $ce->activate();
                }
            }
        }

    }
}

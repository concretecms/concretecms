<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Conversation\Editor\Editor;
use Concrete\Core\Permission\Category;
use Concrete\Core\Utility\Service\Xml;

class ImportConversationEditorsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'groups';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->conversationeditors)) {
            $xml = app(Xml::class);
            foreach ($sx->conversationeditors->editor as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = Editor::add((string) $th['handle'], (string) $th['name'], $pkg);
                if ($xml->getBool($th['activated'])) {
                    $ce->activate();
                }
            }
        }

    }
}

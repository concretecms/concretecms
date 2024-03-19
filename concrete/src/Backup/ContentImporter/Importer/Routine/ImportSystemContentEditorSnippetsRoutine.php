<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Editor\Snippet;
use Concrete\Core\Permission\Category;
use Concrete\Core\Utility\Service\Xml;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportSystemContentEditorSnippetsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'system_content_editor_snippets';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->systemcontenteditorsnippets)) {
            $xml = app(Xml::class);
            foreach ($sx->systemcontenteditorsnippets->snippet as $th) {
                $pkg = static::getPackageObject($th['package']);
                $scs = Snippet::add($th['handle'], $th['name'], $pkg);
                if ($xml->getBool($th['activated'])) {
                    $scs->activate();
                }
            }
        }
    }

}

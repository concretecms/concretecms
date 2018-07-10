<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180709175202 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $db = $this->connection;
        $count = $db->fetchColumn('select count(cnvEditorID) from ConversationEditors where cnvEditorHandle = ?',
            ['redactor']
        );
        if ($count > 0) {
            $db->update('ConversationEditors', [
                'cnvEditorHandle' => 'rich_text',
                'cnvEditorName' => 'Rich Text'
            ], ['cnvEditorHandle' => 'redactor']);
        }
    }
}

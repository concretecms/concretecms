<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5732 extends AbstractMigration
{
    public function getName()
    {
        return '20150117000000';
    }

    public function up(Schema $schema)
    {
        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');
        $ms = $schema->getTable('MultilingualSections');
        $msUpdated = false;
        if (!$ms->hasColumn('msPlurals')) {
            $ms->addColumn('msNumPlurals', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 2));
            $msUpdated = true;
        }
        if (!$ms->hasColumn('msPluralRule')) {
            $ms->addColumn('msPluralRule', 'string', array('notnull' => true, 'length' => 255, 'default' => '(n != 1)'));
            $msUpdated = true;
        }
        if ($msUpdated) {
            $rs = $db->Execute('select cID, msLanguage, msCountry from MultilingualSections');
            while ($row = $rs->FetchRow()) {
                $locale = $row['msLanguage'];
                if ($row['msCountry']) {
                    $locale .= '_' . $row['msCountry'];
                }
                $localeInfo = \Gettext\Utils\Locales::getLocaleInfo($locale);
                if ($localeInfo) {
                    $db->update(
                        'MultilingualSections',
                        array(
                            'msNumPlurals' => $localeInfo['plurals'],
                            'msPluralRule' => $localeInfo['pluralRule'],
                        ),
                        array('cID' => $row['cID'])
                    );
                }
            }
        }
        $mt = $schema->getTable('MultilingualTranslations');
        $mtUpdated = false;
        if (!$mt->hasColumn('msgidPlural')) {
            $mt->addColumn('msgidPlural', 'text', array('notnull' => false));
            $mtUpdated = true;
        }
        if (!$mt->hasColumn('msgstrPlurals')) {
            $mt->addColumn('msgstrPlurals', 'text', array('notnull' => false));
            $mtUpdated = true;
        }
        if ($mtUpdated) {
            $db->Execute("UPDATE MultilingualTranslations SET comments = REPLACE(comments, ':', '\\n') WHERE comments IS NOT NULL");
        }
    }

    public function down(Schema $schema)
    {
    }
}

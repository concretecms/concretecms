<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version5732 extends AbstractMigration
{
    private $updateSectionPlurals = false;
    private $updateMultilingualTranslations = false;

    public function getName()
    {
        return '20150117000000';
    }

    public function up(Schema $schema)
    {
        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');
        $ms = $schema->getTable('MultilingualSections');
        if (!$ms->hasColumn('msNumPlurals')) {
            $ms->addColumn('msNumPlurals', 'integer', array('notnull' => true, 'unsigned' => true, 'default' => 2));
            $this->updateSectionPlurals = true;
        }
        if (!$ms->hasColumn('msPluralRule')) {
            $ms->addColumn('msPluralRule', 'string', array('notnull' => true, 'length' => 255, 'default' => '(n != 1)'));
            $this->updateSectionPlurals = true;
        }
        $mt = $schema->getTable('MultilingualTranslations');
        if (!$mt->hasColumn('msgidPlural')) {
            $mt->addColumn('msgidPlural', 'text', array('notnull' => false));
            $this->updateMultilingualTranslations = true;
        }
        if (!$mt->hasColumn('msgstrPlurals')) {
            $mt->addColumn('msgstrPlurals', 'text', array('notnull' => false));
            $this->updateMultilingualTranslations = true;
        }
    }

    public function postUp(Schema $schema)
    {
        $db = \Database::get();
        if ($this->updateSectionPlurals) {
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
        if ($this->updateMultilingualTranslations) {
            $db->Execute("UPDATE MultilingualTranslations SET comments = REPLACE(comments, ':', '\\n') WHERE comments IS NOT NULL");
        }
    }

    public function down(Schema $schema)
    {
    }
}

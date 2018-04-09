<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\Group;
use Doctrine\DBAL\Schema\Schema;

class Version20150504000000 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    private $updateSectionPlurals = false;
    private $updateMultilingualTranslations = false;

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.4';
    }

    public function installMaintenanceModePermission()
    {
        $pk = Key::getByHandle('view_in_maintenance_mode');
        if (!$pk instanceof Key) {
            $pk = Key::add('admin', 'view_in_maintenance_mode', 'View Site in Maintenance Mode', 'Controls whether a user can access the website when its under maintenance.', false, false);
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = Access::create($pk);
            }
            $adminGroup = Group::getByID(ADMIN_GROUP_ID);
            if ($adminGroup) {
                $adminGroupEntity = GroupEntity::getOrCreate($adminGroup);
                $pa->addListItem($adminGroupEntity);
                $pt = $pk->getPermissionAssignmentObject();
                $pt->assignPermissionAccess($pa);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $pp = $schema->getTable('PagePaths');
        if (!$pp->hasColumn('ppGeneratedFromURLSlugs')) {
            $pp->addColumn('ppGeneratedFromURLSlugs', 'boolean', ['notnull' => true, 'unsigned' => true, 'default' => 0]);
        }

        $ms = $schema->getTable('MultilingualSections');
        if (!$ms->hasColumn('msNumPlurals')) {
            $ms->addColumn('msNumPlurals', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => 2]);
            $this->updateSectionPlurals = true;
        }
        if (!$ms->hasColumn('msPluralRule')) {
            $ms->addColumn('msPluralRule', 'string', ['notnull' => true, 'length' => 400, 'default' => '(n != 1)']);
            $this->updateSectionPlurals = true;
        }
        if (!$ms->hasColumn('msPluralCases')) {
            $ms->addColumn('msPluralCases', 'string', ['notnull' => true, 'length' => 1000, 'default' => "one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …"]);
            $this->updateSectionPlurals = true;
        }
        $mt = $schema->getTable('MultilingualTranslations');
        if (!$mt->hasColumn('msgidPlural')) {
            $mt->addColumn('msgidPlural', 'text', ['notnull' => false]);
            $this->updateMultilingualTranslations = true;
        }
        if (!$mt->hasColumn('msgstrPlurals')) {
            $mt->addColumn('msgstrPlurals', 'text', ['notnull' => false]);
            $this->updateMultilingualTranslations = true;
        }

        $cms = $schema->getTable('ConversationMessages');
        if (!$cms->hasColumn('cnvMessageAuthorName')) {
            $cms->addColumn('cnvMessageAuthorName', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$cms->hasColumn('cnvMessageAuthorEmail')) {
            $cms->addColumn('cnvMessageAuthorEmail', 'string', ['notnull' => false, 'length' => 255]);
        }
        if (!$cms->hasColumn('cnvMessageAuthorWebsite')) {
            $cms->addColumn('cnvMessageAuthorWebsite', 'string', ['notnull' => false, 'length' => 255]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'ConversationPermissionAddMessageAccessList',
            'ConversationSubscriptions',
            'Conversations',
        ]);

        // Subscribe admin to conversations by default, if we have no subscriptions
        $users = \Conversation::getDefaultSubscribedUsers();
        if (count($users) == 0) {
            $admin = \UserInfo::getByID(USER_SUPER_ID);
            if (is_object($admin)) {
                $users = [$admin];
                \Conversation::setDefaultSubscribedUsers($users);
            }
        }

        $db = \Database::get();
        $db->Execute('DROP TABLE IF EXISTS PageStatistics');

        $this->refreshBlockType('page_list');

        $this->refreshBlockType('page_title');

        $this->refreshBlockType('form');

        $c = \Page::getByPath('/dashboard/system/seo/urls');
        if (is_object($c) && !$c->isError()) {
            $c->update(['cName' => 'URLs and Redirection']);
        }

        $this->createSinglePage('/dashboard/system/environment/entities', 'Database Entities', ['meta_keywords' => 'database, entities, doctrine, orm']);

        $pkx = Category::getByHandle('multilingual_section');
        if (!is_object($pkx)) {
            $pkx = Category::add('multilingual_section');
        }
        $pkx->associateAccessEntityType(Type::getByHandle('group'));
        $pkx->associateAccessEntityType(Type::getByHandle('user'));
        $pkx->associateAccessEntityType(Type::getByHandle('group_combination'));

        $db->Execute('alter table QueueMessages modify column body longtext not null');

        $this->updatePermissionDurationObjects();

        $key = Key::getByHandle('add_conversation_message');
        if (is_object($key) && !$key->permissionKeyHasCustomClass()) {
            $key->setPermissionKeyHasCustomClass(true);
        }

        $this->installMaintenanceModePermission();

        $db = \Database::get();
        if ($this->updateSectionPlurals) {
            $rs = $db->Execute('select cID, msLanguage, msCountry from MultilingualSections');
            while ($row = $rs->FetchRow()) {
                $locale = $row['msLanguage'];
                if ($row['msCountry']) {
                    $locale .= '_' . $row['msCountry'];
                }
                $localeInfo = \Gettext\Languages\Language::getById($locale);
                if ($localeInfo) {
                    $pluralCases = [];
                    foreach ($localeInfo->categories as $category) {
                        $pluralCases[] = $category->id . '@' . $category->examples;
                    }
                    $db->update(
                        'MultilingualSections',
                        [
                            'msNumPlurals' => count($localeInfo->categories),
                            'msPluralRule' => $localeInfo->formula,
                            'msPluralCases' => implode("\n", $pluralCases),
                        ],
                        ['cID' => $row['cID']]
                        );
                }
            }
        }
        if ($this->updateMultilingualTranslations) {
            $db->Execute("UPDATE MultilingualTranslations SET comments = REPLACE(comments, ':', '\\n') WHERE comments IS NOT NULL");
        }
    }

    protected function updatePermissionDurationObjects()
    {
        $db = \Database::get();
        $r = $db->Execute('select pdID from PermissionDurationObjects order by pdID asc');
        while ($row = $r->FetchRow()) {
            $pd = Duration::getByID($row['pdID']);
            if (isset($pd->error)) {
                // this is a legacy object. It was serialized from 5.7.3.1 and earlier and used to extend Object.
                // so we take the old pd* parameters and use them as the basis for the standard parameters.
                $pd->setStartDate($pd->pdStartDate);
                $pd->setEndDate($pd->pdEndDate);
                $pd->setStartDateAllDay((bool) $pd->pdStartDateAllDay);
                $pd->setEndDateAllDay((bool) $pd->pdEndDateAllDay);
                if ($pd->pdRepeatPeriod == 'daily') {
                    $pd->setRepeatPeriod(Duration::REPEAT_DAILY);
                } elseif ($pd->pdRepeatPeriod == 'weekly') {
                    $pd->setRepeatPeriod(Duration::REPEAT_WEEKLY);
                } elseif ($pd->pdRepeatPeriod == 'monthly') {
                    $pd->setRepeatPeriod(Duration::REPEAT_MONTHLY);
                } else {
                    $pd->setRepeatPeriod(Duration::REPEAT_NONE);
                }
                if ($pd->pdRepeatEveryNum) {
                    $pd->setRepeatEveryNum($pd->pdRepeatEveryNum);
                }
                if ($pd->pdRepeatPeriodWeeksDays) {
                    $pd->setRepeatPeriodWeekDays($pd->pdRepeatPeriodWeeksDays);
                }
                if ($pd->pdRepeatPeriodMonthsRepeatBy == 'week') {
                    $pd->setRepeatMonthBy(Duration::MONTHLY_REPEAT_WEEKLY);
                } elseif ($pd->pdRepeatPeriodMonthsRepeatBy == 'month') {
                    $pd->setRepeatMonthBy(Duration::MONTHLY_REPEAT_MONTHLY);
                }
                if ($pd->pdRepeatPeriodEnd) {
                    $pd->setRepeatPeriodEnd($pd->pdRepeatPeriodEnd);
                }

                unset($pd->pdStartDate);
                unset($pd->pdEndDate);
                unset($pd->pdStartDateAllDay);
                unset($pd->pdEndDateAllDay);
                unset($pd->pdRepeatPeriod);
                unset($pd->pdRepeatEveryNum);
                unset($pd->pdRepeatPeriodWeeksDays);
                unset($pd->pdRepeatPeriodMonthsRepeatBy);
                unset($pd->pdRepeatPeriodEnd);
                unset($pd->error);
                $pd->save();
            }
        }
    }
}

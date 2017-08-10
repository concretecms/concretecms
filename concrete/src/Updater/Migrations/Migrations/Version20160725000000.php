<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Template;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Site\Service;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\File;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Support\Facade\Application;

class Version20160725000000 extends AbstractMigration
{
    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function prepareInvalidForeignKeys()
    {
        $this->output(t('Removing records with invalid foreign keys...'));
        // Fix orphans of Packages
        $this->nullifyInvalidForeignKey('AttributeKeyCategories', 'pkgID', 'Packages', 'pkgID');
        $this->nullifyInvalidForeignKey('AttributeKeys', 'pkgID', 'Packages', 'pkgID');
        $this->nullifyInvalidForeignKey('AttributeTypes', 'pkgID', 'Packages', 'pkgID');
        // Delete orphans of Users
        $this->deleteInvalidForeignKey('UserAttributeValues', 'uID', 'Users', 'uID');
        // Delete orphans of Files
        $this->deleteInvalidForeignKey('FileVersions', 'fID', 'Files', 'fID');
        // Delete orphans of AttributeTypes
        $this->deleteInvalidForeignKey('AttributeTypeCategories', 'atID', 'AttributeTypes', 'atID');
        // Fix orphans of AttributeTypes
        $this->nullifyInvalidForeignKey('AttributeKeys', 'atID', 'AttributeTypes', 'atID');
        // Delete orphans of AttributeKeys
        $this->deleteInvalidForeignKey('atAddressSettings', 'akID', 'AttributeKeys', 'akID'); // NOT NULL DEFAULT '0',
        $this->deleteInvalidForeignKey('atBooleanSettings', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('atDateTimeSettings', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('atSelectSettings', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('atTextareaSettings', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('atTopicSettings', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('AttributeSetKeys', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('CollectionAttributeValues', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('FileAttributeValues', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('UserAttributeKeys', 'akID', 'AttributeKeys', 'akID');
        $this->deleteInvalidForeignKey('UserAttributeValues', 'akID', 'AttributeKeys', 'akID');
        // Fix orphans of AttributeKeys
        $this->nullifyInvalidForeignKey('AttributeValues', 'akID', 'AttributeKeys', 'akID');
        // Delete orphans of AttributeKeyCategories
        $this->deleteInvalidForeignKey('AttributeTypeCategories', 'akCategoryID', 'AttributeKeyCategories', 'akCategoryID');
        // Fix orphans of AttributeKeyCategories
        $this->nullifyInvalidForeignKey('AttributeKeys', 'akCategoryID', 'AttributeKeyCategories', 'akCategoryID');
        // Delete orphans of AttributeValues
        $this->deleteInvalidForeignKey('atAddress', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atBoolean', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atDateTime', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atDefault', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atFile', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atNumber', 'avID', 'AttributeValues', 'avID');
        $this->deleteInvalidForeignKey('atSocialLinks', 'avID', 'AttributeValues', 'avID');
        // Fix orphans of AttributeValues
        $this->nullifyInvalidForeignKey('FileAttributeValues', 'avID', 'AttributeValues', 'avID');
        $this->nullifyInvalidForeignKey('UserAttributeValues', 'avID', 'AttributeValues', 'avID');
        // Delete orphans of AttributeSets
        $this->deleteInvalidForeignKey('AttributeSetKeys', 'asID', 'AttributeSets', 'asID');
        // Fix Stack orphans 
        $this->deleteInvalidForeignKey('Stacks', 'cID', 'Pages', 'cID');
    }

    protected function nullifyInvalidForeignKeys()
    {
        // We need to perform this action *after* the tables have been migrated because these fields were NOT nullable before
        $this->output(t('Fixing records with invalid foreign keys...'));
        // Fix orphans of Packages
        $this->nullifyInvalidForeignKey('AttributeSets', 'pkgID', 'Packages', 'pkgID');
        // Fix orphans of Files
        $this->nullifyInvalidForeignKey('atFile', 'fID', 'Files', 'fID');
        // Fix orphans of Users
        $this->nullifyInvalidForeignKey('Files', 'uID', 'Users', 'uID');
        // Fix orphans of AttributeKeyCategories
        $this->nullifyInvalidForeignKey('AttributeSets', 'akCategoryID', 'AttributeKeyCategories', 'akCategoryID');
        // Fix orphans of FileStorageLocations
        $this->nullifyInvalidForeignKey('Files', 'fslID', 'FileStorageLocations', 'fslID');
    }

    protected function renameProblematicTables()
    {
        $this->output(t('Renaming problematic tables...'));
        if (!$this->connection->tableExists('_AttributeKeys')) {
            $this->connection->Execute('alter table AttributeKeys rename _AttributeKeys');
        }
        if (!$this->connection->tableExists('_AttributeValues')) {
            $this->connection->Execute('alter table AttributeValues rename _AttributeValues');
        }
        if (!$this->connection->tableExists('_atAddressSettings')) {
            $this->connection->Execute('alter table atAddressSettings rename _atAddressSettings');
            $this->connection->Execute('alter table _atAddressSettings drop primary key, add primary key (akID)');
        }
        if (!$this->connection->tableExists('_atAddressCustomCountries')) {
            $this->connection->Execute('alter table atAddressCustomCountries rename _atAddressCustomCountries');
        }
        if (!$this->connection->tableExists('_atSelectSettings')) {
            $this->connection->Execute('alter table atSelectSettings rename _atSelectSettings');
            $this->connection->Execute('alter table _atSelectSettings drop primary key, add primary key (akID)');
        }
        if (!$this->connection->tableExists('_atSelectOptions')) {
            $this->connection->Execute('alter table atSelectOptions rename _atSelectOptions');
        }
        if (!$this->connection->tableExists('_atSocialLinks')) {
            $this->connection->Execute('alter table atSocialLinks rename _atSocialLinks');
        }
        if (!$this->connection->tableExists('_atSelectOptionsSelected')) {
            $this->connection->Execute('alter table atSelectOptionsSelected rename _atSelectOptionsSelected');
        }
        if (!$this->connection->tableExists('_atSelectedTopics')) {
            $this->connection->Execute('alter table atSelectedTopics rename _atSelectedTopics');
        }
        if (!$this->connection->tableExists('_TreeTopicNodes')) {
            $this->connection->Execute('alter table TreeTopicNodes rename _TreeTopicNodes');
        }
    }

    protected function migrateOldPermissions()
    {
        $this->output(t('Migrating old permissions...'));
        $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', [
            'view_category_tree_node', 'view_topic_category_tree_node',
        ]);
        $this->connection->Execute('update PermissionKeyCategories set pkCategoryHandle = ? where pkCategoryHandle = ?', [
            'category_tree_node', 'topic_category_tree_node',
        ]);
        $folderCategoryID = $this->connection->fetchColumn('select pkCategoryID from PermissionKeyCategories where pkCategoryHandle = ?', ['file_folder']);
        if (!$folderCategoryID) {
            $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', [
                '_add_file', 'add_file',
            ]);
        }
        if (!$this->connection->tableExists('FilePermissionFileTypeAccessList') && $this->connection->tableExists('FileSetPermissionFileTypeAccessList')) {
            $this->connection->Execute('alter table FileSetPermissionFileTypeAccessList rename FilePermissionFileTypeAccessList ');
        }
        if (!$this->connection->tableExists('FilePermissionFileTypeAccessListCustom') && $this->connection->tableExists('FileSetPermissionFileTypeAccessListCustom')) {
            $this->connection->Execute('alter table FileSetPermissionFileTypeAccessListCustom  rename FilePermissionFileTypeAccessListCustom');
        }
    }

    protected function migrateFileManagerPermissions()
    {
        $this->output(t('Migrating file manager permissions...'));
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $this->migrateFileSetManagerPermissions(0, $root);

        // Now let's look for any file sets that have custom permissions
        $r = $this->connection->executeQuery('select * from FileSets where fsOverrideGlobalPermissions = 1');
        while ($row = $r->fetch()) {
            $folder = FileFolder::getNodeByName($row['fsName']);
            if (!is_object($folder)) {
                $folder = $filesystem->addFolder($root, $row['fsName']);
            }
            $this->migrateFileSetManagerPermissions($row['fsID'], $folder);
            // Now we move all the files that were in that set into this folder.
            $r2 = $this->connection->executeQuery('select fID from FileSetFiles where fsID = ?', [$row['fsID']]);
            while ($row2 = $r2->fetch()) {
                $f = \File::getByID($row2['fID']);
                if (is_object($f)) {
                    $node = $f->getFileNodeObject();
                    if (is_object($node)) {
                        $node->move($folder);
                    }
                }
            }
        }
    }

    protected function migrateFileSetManagerPermissions($fsID, FileFolder $folder)
    {
        $this->output(t('Migrating file set permissions...'));
        $r = $this->connection->executeQuery('select fpa.*, pk.pkHandle from FileSetPermissionAssignments fpa inner join PermissionKeys pk on fpa.pkID = pk.pkID where fsID = ?', [$fsID]);
        $permissionsMap = [
            'view_file_set_file' => 'view_file_folder_file',
            'search_file_set' => 'search_file_folder',
            'edit_file_set_file_properties' => 'edit_file_folder_file_properties',
            'edit_file_set_file_contents' => 'edit_file_folder_file_contents',
            'edit_file_set_permissions' => 'edit_file_folder_permissions',
            'copy_file_set_files' => 'copy_file_folder_files',
            'delete_file_set' => 'delete_file_folder',
            'delete_file_set_files' => 'delete_file_folder_files',
            '_add_file' => 'add_file',
        ];

        $count = $this->connection->fetchColumn('select count(*) from TreeNodePermissionAssignments where treeNodeID = ?', [
            $folder->getTreeNodeID(),
        ]);
        if (!$count) {
            $folder->setTreeNodePermissionsToOverride();
            $this->connection->executeQuery('delete from TreeNodePermissionAssignments where treeNodeID = ?', [
                $folder->getTreeNodeID(),
            ]);

            while ($row = $r->fetch()) {
                $mapped = isset($permissionsMap[$row['pkHandle']]) ? $permissionsMap[$row['pkHandle']] : $row['pkHandle'];
                $newPKID = $this->connection->fetchColumn('select pkID from PermissionKeys where pkHandle = ?', [$mapped]);
                $v = [$folder->getTreeNodeID(), $newPKID, $row['paID']];
                $this->connection->executeQuery(
                    'insert into TreeNodePermissionAssignments (treeNodeID, pkID, paID) values (?, ?, ?)', $v
                );
            }

            // Add edit file folder.
            $pk1 = Key::getByHandle('edit_file_folder_permissions');
            $pk2 = Key::getByHandle('edit_file_folder');
            $pk1->setPermissionObject($folder);
            $pk2->setPermissionObject($folder);
            $pa = $pk1->getPermissionAccessObject();
            if (is_object($pa)) {
                $pt = $pk2->getPermissionAssignmentObject();
                $pt->clearPermissionAssignment();
                $pt->assignPermissionAccess($pa);
            }
        }
    }

    protected function updateDoctrineXmlTables()
    {
        $this->output(t('Updating tables found in doctrine xml...'));
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'Pages',
            'Stacks',
            'PageTypes',
            'NotificationPermissionSubscriptionList',
            'NotificationPermissionSubscriptionListCustom',
            'CollectionVersionBlocks',
            'CollectionVersions',
            'TreeNodes',
            'Sessions',
            'TreeFileNodes',
            'UserWorkflowProgress',
            'Users',
        ]);
    }

    protected function prepareProblematicEntityTables()
    {
        $this->output(t('Preparing problematic entity database tables...'));
        // Remove the weird primary keys from the Files table
        $this->connection->executeQuery('alter table Files drop primary key, add primary key (fID)');
    }

    protected function installOtherEntities()
    {
        $entities = [];

        $entityPath = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Entity';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($entityPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if (!$path->isDir()) {
                $path = $path->__toString();
                if (substr(basename($path), 0, 1) != '.') {
                    $path = str_replace([$entityPath, '.php'], '', $path);
                    $entityName = 'Concrete\Core\Entity' . str_replace('/', '\\', $path);
                    $entities[] = $entityName;
                }
            }
        }

        $this->installEntities($entities);
    }

    protected function installEntities($entities)
    {
        // Add tables for new entities or moved entities
        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');

        $em = $this->connection->getEntityManager();
        $cmf = $em->getMetadataFactory();
        $metadatas = [];
        $existingMetadata = $cmf->getAllMetadata();
        foreach ($existingMetadata as $meta) {
            if (in_array($meta->getName(), $entities)) {
                $this->output(t('Installing entity %s...', $meta->getName()));
                $metadatas[] = $meta;
            }
        }

        $sm->installDatabaseFor($metadatas);
    }

    protected function importAttributeKeys()
    {
        // loop through all old attributes and make sure to import them into the new system
        $r = $this->connection->executeQuery('select ak.*, akCategoryHandle from _AttributeKeys ak inner join AttributeKeyCategories akc on ak.akCategoryID = akc.akCategoryID;');
        while ($row = $r->fetch()) {
            $this->output(t('Migrating attribute key %s', $row['akHandle']));
            $table = false;
            $akCategory = null;
            switch ($row['akCategoryHandle']) {
                case 'collection':
                    $table = 'CollectionAttributeKeys';
                    $akCategory = 'pagekey';
                    break;
                case 'file':
                    $table = 'FileAttributeKeys';
                    $akCategory = 'filekey';
                    break;
                case 'user':
                    $akCategory = 'userkey';
                    break;
                default:
                    $akCategory = 'legacykey';
                    break;
            }
            $pkgID = null;
            if ($row['pkgID']) {
                $pkgID = $row['pkgID'];
            }
            $data = [
                'akID' => $row['akID'],
                'akName' => $row['akName'],
                'akHandle' => $row['akHandle'],
                'akIsSearchable' => $row['akIsSearchable'],
                'akIsSearchableIndexed' => $row['akIsSearchableIndexed'],
                'atID' => $row['atID'],
                'akIsInternal' => $row['akIsInternal'],
                'pkgID' => $pkgID,
                'akCategory' => $akCategory,
                'akCategoryID' => $row['akCategoryID'],
            ];
            $keyCount = $this->connection->fetchColumn('select count(*) from AttributeKeys where akID = ?', [$row['akID']]);
            if (!$keyCount) {
                $this->connection->insert('AttributeKeys', $data);
            }
            if ($table) {
                $count = $this->connection->fetchColumn("select count(*) from {$table} where akID = ?", [$row['akID']]);
                if (!$count) {
                    $this->connection->insert($table, ['akID' => $row['akID']]);
                }
            }

            $this->importAttributeKeySettings($row['atID'], $row['akID']);
            switch ($akCategory) {
                case 'pagekey':
                    $rb = $this->connection->executeQuery('select * from CollectionAttributeValues where akID = ?', [$row['akID']]);
                    while ($rowB = $rb->fetch()) {
                        $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID']);
                    }
                    break;
                case 'filekey':
                    $rb = $this->connection->executeQuery('select * from FileAttributeValues where akID = ?', [$row['akID']]);
                    while ($rowB = $rb->fetch()) {
                        $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID']);
                    }
                    break;
                case 'userkey':
                    $rb = $this->connection->executeQuery('select * from UserAttributeValues where akID = ?', [$row['akID']]);
                    while ($rowB = $rb->fetch()) {
                        $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID']);
                    }
                    break;
            }
        }
    }

    protected function migrateAttributeValue($atHandle, $avID)
    {
        switch ($atHandle) {
            case 'address':
                // Nothing to do here.
                break;
            case 'boolean':
                // Nothing to do here.
                break;
            case 'date_time':
                // Nothing to do here.
                break;
            case 'image_file':
                // Nothing to do here.
                break;
            case 'number':
            case 'rating':
            // Nothing to do here.
                break;
            case 'select':
                if (!$this->connection->fetchColumn('select count(avID) from atSelect where avID = ?', [$avID])) {
                    $this->connection->insert('atSelect', ['avID' => $avID]);
                }
                $options = $this->connection->fetchAll('select * from _atSelectOptionsSelected where avID = ?', [$avID]);
                foreach ($options as $option) {
                    if (!$this->connection->fetchColumn('select count(avSelectOptionID) from atSelectOptionsSelected where avSelectOptionID = ? and avID = ?', [$option['atSelectOptionID'], $avID])) {
                        $this->connection->insert('atSelectOptionsSelected', [
                            'avSelectOptionID' => $option['atSelectOptionID'],
                            'avID' => $avID,
                        ]);
                    }
                }
                break;
            case 'social_links':
                if (!$this->connection->fetchColumn('select count(avID) from atSocialLinks where avID = ?', [$avID])) {
                    $this->connection->insert('atSocialLinks', ['avID' => $avID]);
                }
                $links = $this->connection->fetchAll('select * from _atSocialLinks where avID = ?', [$avID]);
                foreach ($links as $link) {
                    $this->connection->insert('atSelectedSocialLinks', [
                        'service' => $link['service'],
                        'serviceInfo' => $link['serviceInfo'],
                        'avID' => $avID,
                    ]);
                }
                break;
            case 'text':
                // Nothing to do here.
                break;
            case 'textarea':
                // Nothing to do here.
                break;
            case 'topics':
                if (!$this->connection->fetchColumn('select count(avID) from atTopic where avID = ?', [$avID])) {
                    $this->connection->insert('atTopic', ['avID' => $avID]);
                }
                $topics = $this->connection->fetchAll('select * from _atSelectedTopics where avID = ?', [$avID]);
                foreach ($topics as $topic) {
                    $this->connection->insert('atSelectedTopics', [
                        'treeNodeID' => $topic['TopicNodeID'],
                        'avID' => $avID,
                    ]);
                }
                break;
        }
    }

    protected function addAttributeValue($atID, $akID, $avID)
    {
        // Create AttributeValueValue Record.
        // Retrieve type
        $atHandle = $this->connection->fetchColumn('select atHandle from AttributeTypes where atID = ?', [$atID]);
        if ($atHandle) {
            $this->migrateAttributeValue($atHandle, $avID);

            // Create AttributeValue record
            if (!$this->connection->fetchColumn('select count(avID) from AttributeValues where avID = ?', [$avID])) {
                $this->connection->insert('AttributeValues', [
                    'akID' => $akID,
                    'avID' => $avID,
                ]);
            }
        }
    }

    protected function importAttributeKeySettings($atID, $akID)
    {
        $row = $this->connection->fetchAssoc('select * from AttributeTypes where atID = ?', [$atID]);
        if ($row['atID']) {
            $this->output(t('Importing attribute key settings %s...', $row['atHandle']));
            switch ($row['atHandle']) {
                case 'address':
                    $count = $this->connection->fetchColumn('select count(*) from atAddressSettings where akID = ?', [$akID]);
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from _atAddressSettings where akID = ?', [$akID]);
                        if ($rowA['akID']) {
                            $countries = $this->connection->fetchAll('select * from _atAddressCustomCountries where akID = ?', [$akID]);
                            if (!$countries) {
                                $countries = [];
                            }
                            $this->connection->insert('atAddressSettings', [
                                'akHasCustomCountries' => $rowA['akHasCustomCountries'],
                                'akDefaultCountry' => $rowA['akDefaultCountry'],
                                'customCountries' => json_encode($countries),
                                'akID' => $akID,
                            ]);
                        }
                    }
                    break;
                case 'boolean':
                    // Nothing to do here.
                    break;
                case 'date_time':
                    // Nothing to do here.
                    break;
                case 'select':
                    $count = $this->connection->fetchColumn('select count(*) from atSelectSettings where akID = ?', [$akID]);
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from _atSelectSettings where akID = ?', [$akID]);
                        if ($rowA['akID']) {
                            $this->connection->insert('atSelectOptionLists', []);
                            $listID = $this->connection->lastInsertId();
                            $this->connection->insert('atSelectSettings', [
                                'akSelectAllowMultipleValues' => $rowA['akSelectAllowMultipleValues'],
                                'akSelectOptionDisplayOrder' => $rowA['akSelectOptionDisplayOrder'],
                                'akSelectAllowOtherValues' => $rowA['akSelectAllowOtherValues'],
                                'avSelectOptionListID' => $listID,
                                'akID' => $akID,
                            ]);

                            $options = $this->connection->fetchAll('select * from _atSelectOptions where akID = ?', [$akID]);
                            foreach ($options as $option) {
                                $this->connection->insert('atSelectOptions', [
                                    'isEndUserAdded' => $option['isEndUserAdded'],
                                    'displayOrder' => $option['displayOrder'],
                                    'value' => $option['value'],
                                    'avSelectOptionID' => $option['ID'],
                                    'avSelectOptionListID' => $listID,
                                ]);
                            }
                        }
                    }
                    break;
                case 'image_file':
                    // Nothing to do here.
                    break;
                case 'number':
                    // Nothing to do here.
                    break;
                case 'rating':
                    // Nothing to do here.
                    break;
                case 'social_links':
                    // Nothing to do here.
                    break;
                case 'text':
                    // Nothing to do here.
                    break;
                case 'textarea':
                    // Nothing to do here.
                    break;
                case 'topics':
                    // Nothing to do here.
                    break;
            }
        }
    }

    protected function importAttributeTypes()
    {
        $types = [
            'express' => 'Express Entity',
            'email' => 'Email Address',
            'telephone' => 'Telephone',
            'url' => 'URL',
        ];
        $categories = ['file', 'user', 'collection'];
        foreach ($types as $handle => $name) {
            $type = Type::getByHandle($handle);
            if (!is_object($type)) {
                $type = Type::add($handle, $name);
                foreach ($categories as $category) {
                    $cat = Category::getByHandle($category);
                    $cat->getController()->associateAttributeKeyType($type);
                }
            }
        }
    }

    protected function addDashboard()
    {
        $this->output(t('Updating Dashboard...'));

        $pageAttributeCategory = Application::getFacadeApplication()->make(PageCategory::class);
        /* @var PageCategory $pageAttributeCategory */
        $availableAttributes = [];
        foreach ([
            'exclude_nav',
            'exclude_search_index',
            'meta_keywords',
        ] as $akHandle) {
            $availableAttributes[$akHandle] = $pageAttributeCategory->getAttributeKeyByHandle($akHandle) ? true : false;
        }

        $page = Page::getByPath('/dashboard/express');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/express');
            $sp->update(['cName' => 'Express', 'cDescription' => 'Express Data Objects']);
        }
        $page = Page::getByPath('/dashboard/express/entries');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/express/entries');
            $sp->update(['cName' => 'View Entries']);
        }
        $page = Page::getByPath('/dashboard/system/express');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express');
            $sp->update(['cName' => 'Express']);
        }
        $page = Page::getByPath('/dashboard/system/express/entities');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities');
            $sp->update(['cName' => 'Data Objects']);
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/express/entities/attributes');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/attributes');
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/express/entities/associations');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/associations');
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/express/entities/forms');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/forms');
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/express/entities/customize_search');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/customize_search');
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/express/entries');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entries');
            $sp->update(['cName' => 'Custom Entry Locations']);
        }
        $page = Page::getByPath('/dashboard/reports/forms/legacy');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/reports/forms/legacy');
            $sp->update(['cName' => 'Form Results']);
            if ($availableAttributes['exclude_search_index']) {
                $sp->setAttribute('exclude_search_index', true);
            }
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/basics/name');
        if (is_object($page) && !$page->isError()) {
            $page->update(['cName' => 'Name & Attributes']);
        }
        $page = Page::getByPath('/dashboard/system/basics/attributes');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/basics/attributes');
            $sp->update(['cName' => 'Custom Attributes']);
            if ($availableAttributes['exclude_search_index']) {
                $sp->setAttribute('exclude_search_index', true);
            }
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
        }
        $page = Page::getByPath('/dashboard/system/registration/global_password_reset');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/registration/global_password_reset');
            $sp->update(['cDescription' => 'Signs out all users, resets all passwords and forces users to choose a new one']);
            if ($availableAttributes['meta_keywords']) {
                $sp->setAttribute('meta_keywords', 'global, password, reset, change password, force, sign out');
            }
        }
        $page = Page::getByPath('/dashboard/system/registration/notification');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/registration/notification');
            $sp->update(['cName' => 'Notification Settings']);
        }
    }

    protected function addBlockTypes()
    {
        $this->output(t('Adding block types...'));
        $desktopSet = \Concrete\Core\Block\BlockType\Set::getByHandle('core_desktop');
        if (!is_object($desktopSet)) {
            $desktopSet = \Concrete\Core\Block\BlockType\Set::add('core_desktop', 'Desktop');
        }

        $expressSet = \Concrete\Core\Block\BlockType\Set::getByHandle('express');
        if (!is_object($expressSet)) {
            $expressSet = \Concrete\Core\Block\BlockType\Set::add('express', 'Express');
        }

        $formSet = \Concrete\Core\Block\BlockType\Set::getByHandle('form');
        if (!is_object($formSet)) {
            $formSet = \Concrete\Core\Block\BlockType\Set::add('form', 'Forms');
        }

        $bt = BlockType::getByHandle('express_form');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('express_form');
        }

        $formSet->addBlockType($bt);

        $bt = BlockType::getByHandle('express_entry_list');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('express_entry_list');
        }

        $expressSet->addBlockType($bt);

        $bt = BlockType::getByHandle('express_entry_detail');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('express_entry_detail');
        }

        $expressSet->addBlockType($bt);

        $bt = BlockType::getByHandle('dashboard_site_activity');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('dashboard_app_status');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('dashboard_featured_theme');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('dashboard_featured_addon');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('dashboard_newsflow_latest');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_site_activity');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_site_activity');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_app_status');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_app_status');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_featured_theme');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_featured_theme');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_featured_addon');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_featured_addon');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_newsflow_latest');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_newsflow_latest');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_latest_form');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_latest_form');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_waiting_for_me');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_waiting_for_me');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('desktop_waiting_for_me');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('desktop_waiting_for_me');
        }

        $desktopSet->addBlockType($bt);

        $bt = BlockType::getByHandle('page_title');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = BlockType::getByHandle('page_list');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = BlockType::getByHandle('next_previous');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = BlockType::getByHandle('autonav');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    protected function addTreeNodeTypes()
    {
        $this->output(t('Adding tree node types...'));
        $this->connection->Execute('update TreeNodeTypes set treeNodeTypeHandle = ? where treeNodeTypeHandle = ?', [
            'category', 'topic_category',
        ]);
        $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', [
            'view_category_tree_node', 'view_topic_category_tree_node',
        ]);
        $this->connection->Execute('update PermissionKeyCategories set pkCategoryHandle = ? where pkCategoryHandle = ?', [
            'category_tree_node', 'topic_category_tree_node',
        ]);
        $results = NodeType::getByHandle('express_entry_results');
        if (!is_object($results)) {
            NodeType::add('express_entry_results');
        }
        $category = NodeType::getByHandle('express_entry_category');
        if (!is_object($category)) {
            NodeType::add('express_entry_category');
        }
        $results = TreeType::getByHandle('express_entry_results');
        if (!is_object($results)) {
            TreeType::add('express_entry_results');
            $tree = ExpressEntryResults::add();
            $node = $tree->getRootTreeNodeObject();
            // Add forms node beneath it.
            \Concrete\Core\Tree\Node\Type\ExpressEntryCategory::add(ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME, $node);
        }
    }

    protected function installDesktops()
    {
        $this->output(t('Installing Desktops...'));
        $template = Template::getByHandle('desktop');
        if (!is_object($template)) {
            Template::add('desktop', t('Desktop'), FILENAME_PAGE_TEMPLATE_DEFAULT_ICON, null, true);
        }
        $type = \Concrete\Core\Page\Type\Type::getByHandle('core_desktop');
        if (!is_object($type)) {
            \Concrete\Core\Page\Type\Type::add([
                'handle' => 'core_desktop',
                'name' => 'Desktop',
                'internal' => true,
            ]);
        }

        $category = Category::getByHandle('collection')->getController();
        $attribute = CollectionKey::getByHandle('is_desktop');
        if (!is_object($attribute)) {
            $key = new PageKey();
            $key->setAttributeKeyHandle('is_desktop');
            $key->setAttributeKeyName('Is Desktop');
            $key->setIsAttributeKeyInternal(true);
            $category->add('boolean', $key);
        }
        $attribute = CollectionKey::getByHandle('desktop_priority');
        if (!is_object($attribute)) {
            $key = new PageKey();
            $key->setAttributeKeyHandle('desktop_priority');
            $key->setAttributeKeyName('Desktop Priority');
            $key->setIsAttributeKeyInternal(true);
            $category->add('number', $key);
        }

        $desktop = Page::getByPath('/dashboard/welcome');
        if (is_object($desktop) && !$desktop->isError()) {
            $desktop->moveToTrash();
        }

        $desktop = Page::getByPath('/desktop');
        if (is_object($desktop) && !$desktop->isError()) {
            $desktop->moveToTrash();
        }

        $page = \Page::getByPath('/account/messages');
        if (is_object($page) && !$page->isError()) {
            $page->moveToTrash();
        }

        // Private Messages tweak
        SinglePage::add('/account/messages');

        $bt = BlockType::getByHandle('rss_displayer');
        if (!is_object($bt)) {
            BlockType::installBlockType('rss_displayer'); // for those those who have removed this block for some reason.
        }

        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/desktops.xml');

        $desktop = Page::getByPath('/dashboard/welcome');
        $desktop->movePageDisplayOrderToTop();

        \Config::save('concrete.misc.login_redirect', 'DESKTOP');
    }

    protected function updateWorkflows()
    {
        $this->output(t('Updating Workflows...'));
        $page = \Page::getByPath('/dashboard/workflow');
        if (is_object($page) && !$page->isError()) {
            $page->moveToTrash();
        }
        $page = \Page::getByPath('/dashboard/system/permissions/workflows');
        if (!is_object($page) || $page->isError()) {
            SinglePage::add('/dashboard/system/permissions/workflows');
        }
    }

    protected function installSite()
    {
        $this->output(t('Installing Site object...'));

        /**
         * @var Service
         */
        $service = \Core::make('site');
        $site = $service->getDefault();
        $em = $this->connection->getEntityManager();

        $type_service = \Core::make('site/type');
        $type = $type_service->getDefault();
        if (!is_object($type)) {
            $type = $type_service->installDefault();
        }

        if (!is_object($site) || $site->getSiteID() < 1) {
            $locale = Localization::BASE_LOCALE;
            if (\Config::get('concrete.multilingual.default_locale')) {
                $locale = \Config::get('concrete.multilingual.default_locale');
            } elseif (\Config::get('concrete.locale')) { // default app language
                $locale = \Config::get('concrete.locale');
            }

            $site = $service->installDefault($locale);

            // migrate name
            $site->setSiteName(\Config::get('concrete.site'));

            // migrate theme
            $c = \Page::getByID(HOME_CID);
            $site->setThemeID($c->getCollectionThemeID());

            $em->persist($site);
            $em->flush();
        }

        $site = $service->getDefault();
        $this->connection->executeQuery('update Pages set siteTreeID = ? where cIsSystemPage = 0', [$site->getSiteTreeID()]);
        $this->connection->executeQuery('update PageTypes set siteTypeID = ? where ptIsInternal = 0', [$type->getSiteTypeID()]);
        // migrate social links
        $links = $em->getRepository('Concrete\Core\Entity\Sharing\SocialNetwork\Link')
            ->findAll();
        foreach ($links as $link) {
            $link->setSite($site);
            $em->persist($link);
        }
        $em->flush();

        $category = Category::getByHandle('site');
        if (!is_object($category)) {
            $category = Category::add('site');
        } else {
            $category = $category->getController();
        }

        $types = Type::getList();
        foreach ($types as $type) {
            $category->associateAttributeKeyType($type);
        }

        $siteConfig = $site->getConfigRepository();

        // migrate bookmark icons
        $favicon_fid = \Config::get('concrete.misc.favicon_fid');
        if ($favicon_fid) {
            $siteConfig->save('misc.favicon_fid', $favicon_fid);
        }
        $iphone_home_screen_thumbnail_fid = \Config::get('concrete.misc.iphone_home_screen_thumbnail_fid');
        if ($iphone_home_screen_thumbnail_fid) {
            $siteConfig->save('misc.iphone_home_screen_thumbnail_fid', $iphone_home_screen_thumbnail_fid);
        }
        $modern_tile_thumbnail_fid = \Config::get('concrete.misc.modern_tile_thumbnail_fid');
        if ($modern_tile_thumbnail_fid) {
            $siteConfig->save('misc.modern_tile_thumbnail_fid', $modern_tile_thumbnail_fid);
        }
        $modern_tile_thumbnail_bgcolor = \Config::get('concrete.misc.modern_tile_thumbnail_bgcolor');
        if ($modern_tile_thumbnail_bgcolor) {
            $siteConfig->save('misc.modern_tile_thumbnail_bgcolor', $modern_tile_thumbnail_bgcolor);
        }

        // migrate url
        $canonical_url = \Config::get('seo.canonical_url');
        if ($canonical_url) {
            $siteConfig->save('seo.canonical_url', $canonical_url);
        }
        $canonical_ssl_url = \Config::get('seo.canonical_ssl_url');
        if ($canonical_ssl_url) {
            $siteConfig->save('seo.canonical_ssl_url', $canonical_ssl_url);
        }

        // migrate tracking code
        $header = \Config::get('seo.tracking.code.header');
        if ($header) {
            $siteConfig->save('seo.tracking.code.header', $header);
        }
        $footer = \Config::get('seo.tracking.code.footer');
        if ($footer) {
            $siteConfig->save('seo.tracking.code.footer', $footer);
        }

        // migrate public profiles
        $r = \Config::get('concrete.user.profiles_enabled');
        if ($r) {
            $siteConfig->save('user.profiles_enabled', $r);
        }
        $r = \Config::get('concrete.user.gravatar.enabled');
        if ($r) {
            $siteConfig->save('user.gravatar.enabled', $r);
        }
        $r = \Config::get('concrete.user.gravatar.max_level');
        if ($r) {
            $siteConfig->save('user.gravatar.max_level', $r);
        }
        $r = \Config::get('concrete.user.gravatar.image_set');
        if ($r) {
            $siteConfig->save('user.gravatar.image_set', $r);
        }
    }

    protected function splittedTrackingCode()
    {
        $this->output(t('Updating tracking code...'));
        $service = \Core::make('site');
        $site = $service->getDefault();
        $config = $site->getConfigRepository();

        $tracking = (array) \Config::get('concrete.seo.tracking', []);
        $trackingCode = array_get($tracking, 'code');
        if (!is_array($trackingCode)) {
            array_set($tracking, 'code', ['header' => '', 'footer' => '']);
            $trackingCode = (string) $trackingCode;
            switch (array_get($tracking, 'code_position')) {
                case 'top':
                    array_set($tracking, 'code.header', $trackingCode);
                    break;
                case 'bottom':
                default:
                    array_set($tracking, 'code.footer', $trackingCode);
                    break;
            }
        }
        unset($tracking['code_position']);
        $config->save('concrete.seo.tracking', $tracking);
    }

    protected function addPermissions()
    {
        $this->output(t('Adding permissions...'));

        CacheLocal::delete('permission_keys', false);

        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/permissions.xml');

        CacheLocal::delete('permission_keys', false);
    }

    protected function cleanupOldPermissions()
    {
        $this->output(t('Cleaning old permissions...'));
        $this->connection->Execute('delete from PermissionKeys where pkHandle = ?', ['_add_file']);
    }

    protected function updateTopics()
    {
        $this->output(t('Updating topics...'));
        $r = $this->connection->executeQuery('select * from _TreeTopicNodes');
        while ($row = $r->fetch()) {
            $this->connection->executeQuery(
                'update TreeNodes set treeNodeName = ? where treeNodeID = ? and treeNodeName = \'\'', [
                    $row['treeNodeTopicName'], $row['treeNodeID'], ]
            );
        }
    }

    protected function updateFileManager()
    {
        $this->output(t('Migrating to new file manager...'));
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        if (!is_object($folder)) {
            $filesystem = new Filesystem();
            $manager = $filesystem->create();
            $folder = $manager->getRootTreeNodeObject();

            $r = $this->connection->executeQuery('select fID from Files');
            while ($row = $r->fetch()) {
                // This is a performance hack
                $f = new \Concrete\Core\Entity\File\File();
                $f->fID = $row['fID'];
                File::add($f, $folder);
            }
        }
    }

    public function addNotifications()
    {
        $this->output(t('Adding notifications...'));
        $adminGroupEntity = GroupEntity::getOrCreate(\Group::getByID(ADMIN_GROUP_ID));
        $adminUserEntity = UserEntity::getOrCreate(\UserInfo::getByID(USER_SUPER_ID));
        $pk = Key::getByHandle('notify_in_notification_center');
        $pa = Access::create($pk);
        $pa->addListItem($adminUserEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);
    }

    protected function updateJobs()
    {
        $this->output(t('Updating jobs...'));
        if (!$job = \Job::getByHandle('update_statistics')) {
            \Job::installByHandle('update_statistics');
        }
    }

    protected function setupSinglePages()
    {
        $this->output(t('Updating single pages...'));
        $siteTreeID = \Core::make('site')->getSite()->getSiteTreeID();
        $pages = [
            // global pages
            ['/dashboard/view.php', 0, 0],
            ['/!trash/view.php', 0, 0],
            ['/login/view.php', 0, 0],
            ['/register/view.php', 0, 0],
            ['/account/view.php', 0, 0],
            ['/page_forbidden.php', 0, 0],
            ['/download_file.php', 0, 0],
            // root pages
            ['/!drafts/view.php', $siteTreeID, 0],
            ['/!stacks/view.php', $siteTreeID, 0],
            ['/page_not_found.php', $siteTreeID, 0],
        ];
        foreach ($pages as $record) {
            $this->connection->executeQuery('update Pages set siteTreeID = ?, cParentID = ? where cFilename = ?', [$record[1], $record[2], $record[0]]);
        }

        // Delete members page if profiles not enabled
        if (!\Config::get('concrete.user.profiles_enabled')) {
            $c = \Page::getByPath('/members');
            $c->moveToTrash();
        }
    }

    protected function installLocales()
    {
        $this->output(t('Updating locales and multilingual sections...'));
        // Update home page so it has no handle. This way we won't make links like /home/services unless
        // people really want that.
        $home = Page::getByID(HOME_CID, 'RECENT');
        $home->update(['cHandle' => '']);

        // Loop through all multilingual sections
        $r = $this->connection->executeQuery('select * from MultilingualSections');
        $sections = [];
        while ($row = $r->fetch()) {
            $sections[] = $row;
        }

        $em = $this->connection->getEntityManager();
        $service = new \Concrete\Core\Localization\Locale\Service($em);
        $site = \Core::make('site')->getSite();

        $redirectToDefaultLocale = \Config::get('concrete.multilingual.redirect_home_to_default_locale');
        $defaultLocale = \Config::get('concrete.multilingual.default_locale');
        $sectionsIncludeHome = false;
        foreach ($sections as $section) {
            if ($section['cID'] == 1) {
                $sectionsIncludeHome = true;
            }
        }

        // Now we have a valid locale object.
        // Case 1: Home Page redirects to default locale, default locale inside the home page. 99% of sites.
        if (!$sectionsIncludeHome && $redirectToDefaultLocale) {
            // Move the home page outside site trees.
            $this->output(t('Moving home page to outside of site trees...'));
            $this->connection->executeQuery('update Pages set siteTreeID = 0 where cID = 1');
        }

        foreach ($sections as $section) {
            $sectionPage = \Page::getByID($section['cID']);
            $this->output(t('Migrating multilingual section: %s...', $sectionPage->getCollectionName()));
            // Create a locale for this section

            if ($site->getDefaultLocale()->getLocale() != $section['msLanguage'] . '_' . $section['msCountry']) {
                // We don't create the locale if it's the default, because we've already created it.
                $locale = $service->add($site, $section['msLanguage'], $section['msCountry']);
            } else {
                $locale = $em->getRepository('Concrete\Core\Entity\Site\Locale')->findOneBy([
                    'msLanguage' => $section['msLanguage'], 'msCountry' => $section['msCountry'],
                ]);
            }

            // Now that we have the locale, let's take the multilingual section and make it the
            // home page for the newly created site tree
            if ($section['cID'] != 1) {
                $tree = $locale->getSiteTree();
                if (!$redirectToDefaultLocale && $locale->getLocale() == $site->getDefaultLocale()->getLocale()) {
                    // Case 2: This is our default locale (/en perhaps) but it is contained within a home
                    // page that we do not redirect from. So we want to actually make the HOME page
                    // the multilingual section – which is already is automatically.

                    // We actually do nothing in this case since this is all already set up automatically earlier.
                } else {
                    $this->output(t('Setting pages for section %s to site tree %s...', $sectionPage->getCollectionName(), $tree->getSiteTreeID()));
                    $tree->setSiteHomePageID($section['cID']);
                    $em->persist($tree);
                    $em->flush();
                    $this->connection->executeQuery('update Pages set cParentID = 0, siteTreeID = ? where cID = ?', [
                        $tree->getSiteTreeID(), $section['cID'],
                    ]);
                    // Now we set all pages in this site tree to the new site tree ID.
                    $children = $sectionPage->getCollectionChildrenArray();
                    foreach ($children as $cID) {
                        $this->connection->executeQuery('update Pages set siteTreeID = ? where cID = ?', [
                            $tree->getSiteTreeID(), $cID,
                        ]);
                    }
                }
            }
        }

        // Case 3 - Home page is the default locale.
        // We don't have to do anything to fulfill this since it's already been taken care of by the previous migrations.
    }

    protected function fixStacks()
    {
        $this->output(t('Updating Stacks and Global Areas...'));
        $this->connection->executeQuery('update Pages inner join Stacks on Pages.cID = Stacks.cID set Pages.siteTreeID = 0');
        $app = Application::getFacadeApplication();
        $site = \Site::getSite();
        if ((int) $this->connection->fetchColumn('select count(*) from SiteLocales where siteID = ?', [$site->getSiteID()]) > 1) {
            $neutrals = [];
            foreach ($this->connection->fetchAll('select stName, stType from Stacks where stMultilingualSection = 0') as $row) {
                $neutrals[$row['stName'] . '@' . $row['stType']] = $row['cID'];
            }
            foreach ($this->connection->fetchAll('select * from Stacks where stMultilingualSection <> 0') as $row) {
                $neutralKey = $row['stName'] . '@' . $row['stType'];
                $child = \Stack::getByID($row['cID']);
                if ($child) {
                    if (isset($neutrals[$neutralKey]) && is_numeric(isset($neutrals[$neutralKey]))) {
                        if ($row['stType'] == \Stack::ST_TYPE_GLOBAL_AREA) {
                            $neutrals[$neutralKey] = \Page::getByID($neutrals[$neutralKey]);
                            if ($neutrals[$neutralKey] && $neutrals[$neutralKey]->isError()) {
                                $neutrals[$neutralKey] = null;
                            }
                        } else {
                            $neutrals[$neutralKey] = \Stack::getByID($neutrals[$neutralKey]);
                        }
                        if (!$neutrals[$neutralKey]) {
                            unset($neutrals[$neutralKey]);
                        }
                    }
                    if (!isset($neutrals[$neutralKey])) {
                        if ($row['stType'] == \Stack::ST_TYPE_GLOBAL_AREA) {
                            $neutrals[$neutralKey] = \Stack::addGlobalArea($row['stName']);
                        } else {
                            $neutrals[$neutralKey] = \Stack::addStack($row['stName']);
                        }
                    }
                    $child->move($neutrals[$neutralKey]);
                }
            }
        } else {
            // Consider all the stacks and global areas as "neutral version"
            $this->connection->executeQuery('update Stacks set stMultilingualSection = 0');
        }
    }

    public function up(Schema $schema)
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->prepareInvalidForeignKeys();
        $this->renameProblematicTables();
        $this->updateDoctrineXmlTables();
        $this->prepareProblematicEntityTables();
        $this->installEntities(['Concrete\Core\Entity\File\File', 'Concrete\Core\Entity\File\Version']);
        $this->installOtherEntities();
        $this->installSite();
        $this->importAttributeTypes();
        $this->migrateOldPermissions();
        $this->addPermissions();
        $this->importAttributeKeys();
        $this->addDashboard();
        $this->updateFileManager();
        $this->migrateFileManagerPermissions();
        $this->addBlockTypes();
        $this->updateTopics();
        $this->updateWorkflows();
        $this->addTreeNodeTypes();
        $this->installDesktops();
        $this->updateJobs();
        $this->setupSinglePages();
        $this->addNotifications();
        $this->splittedTrackingCode();
        $this->cleanupOldPermissions();
        $this->installLocales();
        $this->fixStacks();
        $this->nullifyInvalidForeignKeys();
        $this->connection->Execute('set foreign_key_checks = 1');
    }

    public function down(Schema $schema)
    {
    }
}

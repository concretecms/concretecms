<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Entity\Attribute\Key\Type\BooleanType;
use Concrete\Core\Entity\Attribute\Key\Type\NumberType;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Set\Set;
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
use Concrete\Core\User\Group\Group;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Support\Facade\Facade;

class Version20160725000000 extends AbstractMigration
{
    protected function renameProblematicTables()
    {
        if (!$this->connection->tableExists('_AttributeKeys')) {
            $this->connection->Execute('alter table AttributeKeys rename _AttributeKeys');
        }
        if (!$this->connection->tableExists('_AttributeValues')) {
            $this->connection->Execute('alter table AttributeValues rename _AttributeValues');
        }
        if (!$this->connection->tableExists('_CollectionAttributeValues')) {
            $this->connection->Execute('alter table CollectionAttributeValues rename _CollectionAttributeValues');
        }
        if (!$this->connection->tableExists('_FileAttributeValues')) {
            $this->connection->Execute('alter table FileAttributeValues rename _FileAttributeValues');
        }
        if (!$this->connection->tableExists('_UserAttributeValues')) {
            $this->connection->Execute('alter table UserAttributeValues rename _UserAttributeValues');
        }
        if (!$this->connection->tableExists('_TreeTopicNodes')) {
            $this->connection->Execute('alter table TreeTopicNodes rename _TreeTopicNodes');
        }
    }

    protected function migrateOldPermissions()
    {
        $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', array(
            'view_category_tree_node', 'view_topic_category_tree_node',
        ));
        $this->connection->Execute('update PermissionKeyCategories set pkCategoryHandle = ? where pkCategoryHandle = ?', array(
            'category_tree_node', 'topic_category_tree_node',
        ));
        $folderCategoryID = $this->connection->fetchColumn('select pkCategoryID from PermissionKeyCategories where pkCategoryHandle = ?', array('file_folder'));
        if (!$folderCategoryID) {
            $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', array(
                '_add_file', 'add_file',
            ));
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
            $r2 = $this->connection->executeQuery('select fID from FileSetFiles where fsID = ?', array($row['fsID']));
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
        $r = $this->connection->executeQuery('select fpa.*, pk.pkHandle from FileSetPermissionAssignments fpa inner join PermissionKeys pk on fpa.pkID = pk.pkID where fsID = ?', array($fsID));
        $permissionsMap = array(
            'view_file_set_file' => 'view_file_folder_file',
            'search_file_set' => 'search_file_folder',
            'edit_file_set_file_properties' => 'edit_file_folder_file_properties',
            'edit_file_set_file_contents' =>  'edit_file_folder_file_contents',
            'edit_file_set_permissions' =>  'edit_file_folder_permissions',
            'copy_file_set_files' =>  'copy_file_folder_files',
            'delete_file_set' =>  'delete_file_folder',
            'delete_file_set_files' => 'delete_file_folder_files',
            '_add_file' => 'add_file',
        );

        $count = $this->connection->fetchColumn('select count(*) from TreeNodePermissionAssignments where treeNodeID = ?', array(
            $folder->getTreeNodeID()
        ));
        if (!$count) {

            $folder->setTreeNodePermissionsToOverride();
            $this->connection->executeQuery('delete from TreeNodePermissionAssignments where treeNodeID = ?', array(
                $folder->getTreeNodeID()
            ));

            while ($row = $r->fetch()) {
                $mapped = $permissionsMap[$row['pkHandle']];
                $newPKID = $this->connection->fetchColumn('select pkID from PermissionKeys where pkHandle = ?', array($mapped));
                $v = array($folder->getTreeNodeID(), $newPKID, $row['paID']);
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

        // Loop through all file sets that have custom permissions and create folders from them,
        // preserving the file permssions on the folders themselves


    }

    protected function updateDoctrineXmlTables()
    {
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'Pages',
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
        ));
    }

    protected function prepareProblematicEntityTables()
    {
        // Remove the weird primary keys from the Files table
        $this->connection->executeQuery('alter table Files drop primary key, add primary key (fID)');

        $this->connection->executeQuery('update AttributeTypes set pkgID = null where pkgID = 0');
    }


    /**
     * Loop through all installed packages and write the metadata setting for packages
     * to the database.php config in genereated_overrides
     */
    protected function createMetaDataConfigurationForPackages(){

        $r = $this->connection->executeQuery('SELECT * FROM packages WHERE pkgIsInstalled = 1;');
        
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $packageService = $app->make('Concrete\Core\Package\PackageService');

        while ($row = $r->fetch()) {
            $pkgClass = \Concrete\Core\Package\PackageService::getClass($row['pkgHandle']);
            if(!empty($pkgClass->getPackageMetadataPaths())){
                $packageService->savePackageMetadataDriverToConfig($pkgClass);
            }
        }
    }

    protected function installOtherEntities()
    {
        $entities = array();

        $entityPath = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Entity';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($entityPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if (!$path->isDir()) {
                $path = $path->__toString();
                if (substr(basename($path), 0, 1) != '.') {
                    $path = str_replace(array($entityPath, '.php'), '', $path);
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
        $metadatas = array();
        $existingMetadata = $cmf->getAllMetadata();
        foreach($existingMetadata as $meta) {
            if (in_array($meta->getName(), $entities)) {
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
            }
            $pkgID = null;
            if ($row['pkgID']) {
                $pkgID = $row['pkgID'];
            }
            $data = array(
                'akID' => $row['akID'],
                'akName' => $row['akName'],
                'akHandle' => $row['akHandle'],
                'akIsSearchable' => $row['akIsSearchable'],
                'akIsSearchableIndexed' => $row['akIsSearchableIndexed'],
                'akIsInternal' => $row['akIsInternal'],
                'pkgID' => $pkgID,
                'akCategory' => $akCategory,
            );
            $keyCount = $this->connection->fetchColumn("select count(*) from AttributeKeys where akID = ?", array($row['akID']));
            if (!$keyCount) {
                $this->connection->insert('AttributeKeys', $data);
            }
            if ($table) {
                $count = $this->connection->fetchColumn("select count(*) from {$table} where akID = ?", array($row['akID']));
                if (!$count) {
                    $this->connection->insert($table, array('akID' => $row['akID']));
                }
            }

            $this->importAttributeKeyType($row['atID'], $row['akID']);
            switch ($akCategory) {
                case 'pagekey':
                    $rb = $this->connection->executeQuery("select * from _CollectionAttributeValues where akID = ?", array($row['akID']));
                    while ($rowB = $rb->fetch()) {
                        $avrID = $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID'], 'page');
                        if ($avrID) {
                            $this->connection->insert('CollectionAttributeValues', [
                                'cID' => $rowB['cID'],
                                'cvID' => $rowB['cvID'],
                                'avrID' => $avrID,
                            ]);
                        }
                    }
                    break;
                case 'filekey':
                    $rb = $this->connection->executeQuery("select * from _FileAttributeValues where akID = ?", array($row['akID']));
                    while ($rowB = $rb->fetch()) {
                        $avrID = $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID'], 'page');
                        if ($avrID) {
                            $this->connection->insert('FileAttributeValues', [
                                'fID' => $rowB['fID'],
                                'fvID' => $rowB['fvID'],
                                'avrID' => $avrID,
                            ]);
                        }
                    }
                    break;
                case 'userkey':
                    $rb = $this->connection->executeQuery("select * from _UserAttributeValues where akID = ?", array($row['akID']));
                    while ($rowB = $rb->fetch()) {
                        $avrID = $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID'], 'page');
                        if ($avrID) {
                            $this->connection->insert('UserAttributeValues', [
                                'avrID' => $avrID,
                            ]);
                        }
                    }
                    break;
            }
        }
    }

    protected function loadAttributeValue($atHandle, $legacyAVID, $avID)
    {
        switch ($atHandle) {
            case 'address':
                $row = $this->connection->fetchAssoc('select * from atAddress where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('AddressAttributeValues', $row);
                break;
            case 'boolean':
                $value = $this->connection->fetchColumn('select value from atBoolean where avID = ?', [$legacyAVID]);
                $this->connection->insert('BooleanAttributeValues', ['value' => $value, 'avID' => $avID]);
                break;
            case 'date_time':
                $row = $this->connection->fetchAssoc('select * from atDateTime where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('DateTimeAttributeValues', $row);
                break;
            case 'image_file':
                $row = $this->connection->fetchAssoc('select * from atFile where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('ImageFileAttributeValues', $row);
                break;
            case 'number':
            case 'rating':
                $row = $this->connection->fetchAssoc('select * from atNumber where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('NumberAttributeValues', $row);
                break;
            case 'select':
                $this->connection->insert('SelectAttributeValues', array('avID' => $avID));
                $options = $this->connection->fetchAll('select * from atSelectOptionsSelected where avID = ?', [$legacyAVID]);
                foreach ($options as $option) {
                    $this->connection->insert('SelectAttributeValueSelectedOptions', array(
                        'avSelectOptionID' => $option['atSelectOptionID'],
                        'avID' => $avID,
                    ));
                }
                break;
            case 'social_links':
                $this->connection->insert('SocialLinksAttributeValues', array('avID' => $avID));
                $links = $this->connection->fetchAll('select * from atSocialLinks where avID = ?', [$legacyAVID]);
                foreach ($links as $link) {
                    $this->connection->insert('SocialLinksAttributeSelectedLinks', array(
                        'service' => $link['service'],
                        'serviceInfo' => $link['serviceInfo'],
                        'avID' => $avID,
                    ));
                }
                break;
            case 'text':
                $row = $this->connection->fetchAssoc('select * from atDefault where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('TextAttributeValues', $row);
                break;
            case 'textarea':
                $row = $this->connection->fetchAssoc('select * from atDefault where avID = ?', [$legacyAVID]);
                $row['avID'] = $avID;
                $this->connection->insert('TextareaAttributeValues', $row);
                break;
            case 'topics':
                $this->connection->insert('TopicAttributeValues', array('avID' => $avID));
                $topics = $this->connection->fetchAll('select * from atSelectedTopics where avID = ?', [$legacyAVID]);
                foreach ($topics as $topic) {
                    $this->connection->insert('TopicAttributeSelectedTopics', array(
                        'treeNodeID' => $topic['TopicNodeID'],
                        'avID' => $avID,
                    ));
                }
                break;
        }
    }

    protected function addAttributeValue($atID, $akID, $legacyAVID, $type)
    {
        // Create AttributeValueValue Record.
        // Retrieve type
        $atHandle = $this->connection->fetchColumn('select atHandle from AttributeTypes where atID = ?', array($atID));
        if ($atHandle) {
            $valueType = strtolower(preg_replace("/[^A-Za-z]/", '', $atHandle)) . 'value';
            $type = $type . 'value';

            $this->connection->insert('AttributeValueValues', ['type' => $valueType]);
            $avID = $this->connection->lastInsertId();

            $this->loadAttributeValue($atHandle, $legacyAVID, $avID);

            // Create AttributeValue record
            $this->connection->insert('AttributeValues', [
                'akID' => $akID,
                'avID' => $avID,
                'type' => $type,
            ]);

            return $this->connection->lastInsertId();
        }
    }

    protected function importAttributeKeyType($atID, $akID)
    {
        $row = $this->connection->fetchAssoc('select * from AttributeTypes where atID = ?', array($atID));
        if ($row['atID']) {
            $akTypeID = $this->connection->fetchColumn("select akTypeID from AttributeKeyTypes where akID = ?", array($akID));
            $type = strtolower(preg_replace("/[^A-Za-z]/", '', $row['atHandle'])) . 'type';
            if (!$akTypeID) {
                $this->connection->insert('AttributeKeyTypes', ['akTypeHandle' => $row['atHandle'], 'akID' => $akID, 'type' => $type]);
                $akTypeID = $this->connection->lastInsertId();
            }
            switch ($row['atHandle']) {
                case 'address':
                    $count = $this->connection->fetchColumn('select count(*) from AddressAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atAddressSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $countries = $this->connection->fetchAll('select * from atAddressCustomCountries where akID = ?', array($akID));
                            if (!$countries) {
                                $countries = array();
                            }
                            $this->connection->insert('AddressAttributeKeyTypes', [
                                'akHasCustomCountries' => $rowA['akHasCustomCountries'],
                                'akDefaultCountry' => $rowA['akDefaultCountry'],
                                'customCountries' => json_encode($countries),
                                'akTypeID' => $akTypeID,
                            ]);
                        }
                    }
                    break;
                case 'boolean':
                    $count = $this->connection->fetchColumn('select count(*) from BooleanAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atBooleanSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $this->connection->insert('BooleanAttributeKeyTypes', [
                                'akCheckedByDefault' => $rowA['akCheckedByDefault'],
                                'akTypeID' => $akTypeID,
                            ]);
                        }
                    }
                    break;
                case 'date_time':
                    $count = $this->connection->fetchColumn('select count(*) from DateTimeAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atDateTimeSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $this->connection->insert('DateTimeAttributeKeyTypes', [
                                'akDateDisplayMode' => $rowA['akDateDisplayMode'],
                                'akTypeID' => $akTypeID,
                            ]);
                        }
                    }
                    break;
                case 'select':
                    $count = $this->connection->fetchColumn('select count(*) from SelectAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atSelectSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $this->connection->insert('SelectAttributeValueOptionLists', []);
                            $listID = $this->connection->lastInsertId();
                            $this->connection->insert('SelectAttributeKeyTypes', [
                                'akSelectAllowMultipleValues' => $rowA['akSelectAllowMultipleValues'],
                                'akSelectOptionDisplayOrder' => $rowA['akSelectOptionDisplayOrder'],
                                'akSelectAllowOtherValues' => $rowA['akSelectAllowOtherValues'],
                                'avSelectOptionListID' => $listID,
                                'akTypeID' => $akTypeID,
                            ]);

                            $options = $this->connection->fetchAll('select * from atSelectOptions where akID = ?', array($akID));
                            foreach ($options as $option) {
                                $this->connection->insert('SelectAttributeValueOptions', [
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
                    $count = $this->connection->fetchColumn('select count(*) from ImageFileAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $this->connection->insert('ImageFileAttributeKeyTypes', ['akFileManagerMode' => 0, 'akTypeID' => $akTypeID]);
                    }
                    break;
                case 'number':
                    $count = $this->connection->fetchColumn('select count(*) from NumberAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $this->connection->insert('NumberAttributeKeyTypes', ['akTypeID' => $akTypeID]);
                    }
                    break;
                case 'rating':
                    $count = $this->connection->fetchColumn('select count(*) from RatingAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $this->connection->insert('RatingAttributeKeyTypes', ['akTypeID' => $akTypeID]);
                    }
                    break;
                case 'social_links':
                    $count = $this->connection->fetchColumn('select count(*) from SocialLinksAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $this->connection->insert('SocialLinksAttributeKeyTypes', ['akTypeID' => $akTypeID]);
                    }
                    break;
                case 'text':
                    $count = $this->connection->fetchColumn('select count(*) from TextAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $this->connection->insert('TextAttributeKeyTypes', ['akTypeID' => $akTypeID]);
                    }
                    break;
                case 'textarea':
                    $count = $this->connection->fetchColumn('select count(*) from TextareaAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atTextareaSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $this->connection->insert('TextareaAttributeKeyTypes', [
                                'akTextareaDisplayMode' => $rowA['akTextareaDisplayMode'],
                                'akTypeID' => $akTypeID,
                            ]);
                        }
                    }
                    break;
                case 'topics':
                    $count = $this->connection->fetchColumn('select count(*) from TopicsAttributeKeyTypes where akTypeID = ?', array($akTypeID));
                    if (!$count) {
                        $rowA = $this->connection->fetchAssoc('select * from atTopicSettings where akID = ?', array($akID));
                        if ($rowA['akID']) {
                            $this->connection->insert('TopicsAttributeKeyTypes', [
                                'akTopicParentNodeID' => $rowA['akTopicParentNodeID'],
                                'akTopicTreeID' => $rowA['akTopicTreeID'],
                                'akTypeID' => $akTypeID,
                            ]);
                        }
                    }
                    break;
            }
        }
    }

    protected function importAttributeTypes()
    {
        $types = array(
            'express' => 'Express Entity',
            'email' => 'Email Address',
            'telephone' => 'Telephone',
            'url' => 'URL',
        );
        $categories = array('file', 'user', 'collection');
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
        $page = Page::getByPath('/dashboard/express');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/express');
            $sp->update(array('cName' => 'Express', 'cDescription' => 'Express Data Objects'));
        }
        $page = Page::getByPath('/dashboard/express/entries');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/express/entries');
            $sp->update(array('cName' => 'View Entries'));
        }
        $page = Page::getByPath('/dashboard/system/express');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express');
            $sp->update(array('cName' => 'Express'));
        }
        $page = Page::getByPath('/dashboard/system/express/entities');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities');
            $sp->update(array('cName' => 'Data Objects'));
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/express/entities/attributes');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/attributes');
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/express/entities/associations');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/associations');
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/express/entities/forms');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/forms');
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/express/entities/customize_search');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities/customize_search');
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/express/entries');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entries');
            $sp->update(array('cName' => 'Custom Entry Locations'));
        }
        $page = Page::getByPath('/dashboard/reports/forms/legacy');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/reports/forms/legacy');
            $sp->update(array('cName' => 'Form Results'));
            $sp->setAttribute('exclude_search_index', true);
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/basics/name');
        if (is_object($page) && !$page->isError()) {
            $page->update(array('cName' => 'Name & Attributes'));
        }
        $page = Page::getByPath('/dashboard/system/basics/attributes');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/basics/attributes');
            $sp->update(array('cName' => 'Custom Attributes'));
            $sp->setAttribute('exclude_search_index', true);
            $sp->setAttribute('exclude_nav', true);
        }
        $page = Page::getByPath('/dashboard/system/registration/global_password_reset');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/registration/global_password_reset');
            $sp->update(array('cDescription' => 'Signs out all users, resets all passwords and forces users to choose a new one'));
            $sp->setAttribute('meta_keywords', 'global, password, reset, change password, force, sign out');
        }
        $page = Page::getByPath('/dashboard/system/registration/notification');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/registration/notification');
            $sp->update(array('cName' => 'Notification Settings'));
        }

    }

    protected function addBlockTypes()
    {
        $bt = BlockType::getByHandle('express_form');
        if (!is_object($bt)) {
            BlockType::installBlockType('express_form');
        }

        $bt = BlockType::getByHandle('dashboard_site_activity');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_site_activity');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_site_activity');
        }

        $bt = BlockType::getByHandle('dashboard_app_status');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_app_status');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_app_status');
        }

        $bt = BlockType::getByHandle('dashboard_featured_theme');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_featured_theme');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_featured_theme');
        }

        $bt = BlockType::getByHandle('dashboard_featured_addon');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_featured_addon');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_featured_addon');
        }

        $bt = BlockType::getByHandle('dashboard_newsflow_latest');
        if (is_object($bt)) {
            $bt->delete();
        }

        $bt = BlockType::getByHandle('desktop_newsflow_latest');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_newsflow_latest');
        }

        $bt = BlockType::getByHandle('desktop_latest_form');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_latest_form');
        }
        $bt = BlockType::getByHandle('desktop_waiting_for_me');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_waiting_for_me');
        }

        $bt = BlockType::getByHandle('express_entry_list');
        if (!is_object($bt)) {
            BlockType::installBlockType('express_entry_list');
        }

        $bt = BlockType::getByHandle('express_entry_detail');
        if (!is_object($bt)) {
            BlockType::installBlockType('express_entry_detail');
        }

        $bt = BlockType::getByHandle('desktop_waiting_for_me');
        if (!is_object($bt)) {
            BlockType::installBlockType('desktop_waiting_for_me');
        }

        $bt = BlockType::getByHandle('page_title');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = BlockType::getByHandle('page_list');
        if (is_object($bt)) {
            $bt->refresh();
        }
    }

    protected function addTreeNodeTypes()
    {
        $this->connection->Execute('update TreeNodeTypes set treeNodeTypeHandle = ? where treeNodeTypeHandle = ?', array(
            'category', 'topic_category',
        ));
        $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', array(
            'view_category_tree_node', 'view_topic_category_tree_node',
        ));
        $this->connection->Execute('update PermissionKeyCategories set pkCategoryHandle = ? where pkCategoryHandle = ?', array(
            'category_tree_node', 'topic_category_tree_node',
        ));
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
            \Concrete\Core\Tree\Node\Type\Category::add(ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME, $node);
        }
    }

    protected function installDesktops()
    {
        $template = Template::getByHandle('desktop');
        if (!is_object($template)) {
            Template::add('desktop', t('Desktop'), FILENAME_PAGE_TEMPLATE_DEFAULT_ICON, null, true);
        }
        $type = \Concrete\Core\Page\Type\Type::getByHandle('core_desktop');
        if (!is_object($type)) {
            \Concrete\Core\Page\Type\Type::add(array(
                'handle' => 'core_desktop',
                'name' => 'Desktop',
                'internal' => true
            ));
        }

        $category = Category::getByHandle('collection')->getController();
        $attribute = CollectionKey::getByHandle('is_desktop');
        if (!is_object($attribute)) {
            $type = new BooleanType();
            $key = new PageKey();
            $key->setAttributeKeyHandle('is_desktop');
            $key->setAttributeKeyName('Is Desktop');
            $key->setIsAttributeKeyInternal(true);
            $category->add($type, $key);
        }
        $attribute = CollectionKey::getByHandle('desktop_priority');
        if (!is_object($attribute)) {
            $type = new NumberType();
            $key = new PageKey();
            $key->setAttributeKeyHandle('desktop_priority');
            $key->setAttributeKeyName('Desktop Priority');
            $key->setIsAttributeKeyInternal(true);
            $category->add($type, $key);
        }

        $desktop = Page::getByPath('/dashboard/welcome');
        if (is_object($desktop) && !$desktop->isError()) {
            $desktop->moveToTrash();
        }

        $desktop = Page::getByPath('/desktop');
        if (is_object($desktop) && !$desktop->isError()) {
            $desktop->moveToTrash();
        }

        $page = \Page::getByPath("/account/messages");
        if (is_object($page) && !$page->isError()) {
            $page->moveToTrash();
        }

        // Private Messages tweak
        SinglePage::add('/account/messages');

        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/desktops.xml');

        $desktop = Page::getByPath('/dashboard/welcome');
        $desktop->movePageDisplayOrderToTop();

        \Config::save('concrete.misc.login_redirect', 'DESKTOP');
    }

    protected function updateWorkflows()
    {
        $page = \Page::getByPath("/dashboard/workflow");
        if (is_object($page) && !$page->isError()) {
            $page->moveToTrash();
        }
        $page = \Page::getByPath("/dashboard/system/permissions/workflows");
        if (!is_object($page) || $page->isError()) {
            SinglePage::add('/dashboard/system/permissions/workflows');
        }
    }

    protected function installSite()
    {
        /**
         * @var $service Service
         */
        $service = \Core::make('site');
        $site = $service->getDefault();
        if (!is_object($site) || $site->getSiteID() < 1) {
            $site = $service->installDefault();

            $this->connection->executeQuery('update Pages set siteID = ? where cIsSystemPage = 0', [$site->getSiteID()]);

            $em = $this->connection->getEntityManager();

            // migrate name
            $site->setSiteName(\Config::get('concrete.site'));

            // migrate theme
            $c = \Page::getByID(HOME_CID);
            $site->setThemeID($c->getCollectionThemeID());

            $em->persist($site);

            // migrate social links
            $links = $em->getRepository('Concrete\Core\Entity\Sharing\SocialNetwork\Link')
                ->findAll();
            foreach($links as $link) {
                $link->setSite($site);
                $em->persist($link);
            }
            $em->flush();
        }

        $category = Category::getByHandle('site');
        if (!is_object($category)) {
            $category = Category::add('site');
        } else {
            $category = $category->getController();
        }

        $types = Type::getList();
        foreach($types as $type) {
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

        CacheLocal::delete('permission_keys', false);

        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/permissions.xml');

        CacheLocal::delete('permission_keys', false);
    }

    protected function cleanupOldPermissions()
    {
        $this->connection->Execute('delete from PermissionKeys where pkHandle = ?', array('_add_file'));
    }

    protected function updateTopics()
    {
        $r = $this->connection->executeQuery('select * from _TreeTopicNodes');
        while ($row = $r->fetch()) {
            $this->connection->executeQuery(
                'update TreeNodes set treeNodeName = ? where treeNodeID = ? and treeNodeName = \'\'', [
                    $row['treeNodeTopicName'], $row['treeNodeID']]
            );
        }
    }

    protected function updateFileManager()
    {
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        if (!is_object($folder)) {
            $filesystem = new Filesystem();
            $manager = $filesystem->create();
            $folder = $manager->getRootTreeNodeObject();

            $r = $this->connection->executeQuery('select fID from Files');
            while ($row = $r->fetch()) {
                $f = \Concrete\Core\File\File::getByID($row['fID']);
                File::add($f, $folder);
            }
        }
    }

    public function addNotifications()
    {
        $adminGroupEntity = GroupEntity::getOrCreate(\Group::getByID(ADMIN_GROUP_ID));
        $adminUserEntity = UserEntity::getOrCreate(\UserInfo::getByID(USER_SUPER_ID));
        $pk = Key::getByHandle('notify_in_notification_center');
        $pa = Access::create($pk);
        $pa->addListItem($adminUserEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);
    }

    public function up(Schema $schema)
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->renameProblematicTables();
        $this->updateDoctrineXmlTables();
        $this->prepareProblematicEntityTables();
        $this->createMetaDataConfigurationForPackages();
        $this->installEntities(array('Concrete\Core\Entity\File\File', 'Concrete\Core\Entity\File\Version'));
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
        $this->addNotifications();
        $this->splittedTrackingCode();
        $this->cleanupOldPermissions();
        $this->connection->Execute('set foreign_key_checks = 1');
    }

    public function down(Schema $schema)
    {
    }
}

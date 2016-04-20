<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Entity\Attribute\Key\Type\BooleanType;
use Concrete\Core\Entity\Attribute\Key\Type\NumberType;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;

class Version20160420000000 extends AbstractMigration
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
    }

    protected function updateDoctrineXmlTables()
    {
        // Update tables that still exist in db.xml
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'CollectionVersions',
            'TreeNodes',
            'Sessions',
            'UserWorkflowProgress',
            'Users',
        ));
    }

    protected function installEntities()
    {
        // Add tables for new entities or moved entities
        $sm = \Core::make('Concrete\Core\Database\DatabaseStructureManager');
        $entities = array();

        // Now we fill the rest of the class names recursively from the entity directory, since it's
        // entirely new
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
                    $entities[] = 'Concrete\Core\Entity' . str_replace('/', '\\', $path);
                }
            }
        }

        $em = $this->connection->getEntityManager();
        $cmf = $em->getMetadataFactory();
        $metadatas = array();
        foreach ($cmf->getAllMetadata() as $meta) {
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
            switch($row['akCategoryHandle']) {
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
            switch($akCategory) {
                case 'pagekey':
                    $rb = $this->connection->executeQuery("select * from _CollectionAttributeValues where akID = ?", array($row['akID']));
                    while ($rowB = $rb->fetch()) {
                        $avrID = $this->addAttributeValue($row['atID'], $row['akID'], $rowB['avID'], 'page');
                        if ($avrID) {
                            $this->connection->insert('CollectionAttributeValues', [
                                'cID' => $rowB['cID'],
                                'cvID' => $rowB['cvID'],
                                'avrID' => $avrID
                            ]);
                        }
                    }
                    break;
            }
        }
    }

    protected function loadAttributeValue($atHandle, $legacyAVID, $avID)
    {
        switch($atHandle) {
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
                foreach($options as $option) {
                    $this->connection->insert('SelectAttributeValueSelectedOptions', array(
                        'avSelectOptionID' => $option['atSelectOptionID'],
                        'avID' => $avID
                    ));
                }
                break;
            case 'social_links':
                $this->connection->insert('SocialLinksAttributeValues', array('avID' => $avID));
                $links = $this->connection->fetchAll('select * from atSocialLinks where avID = ?', [$legacyAVID]);
                foreach($links as $link) {
                    $this->connection->insert('SocialLinksAttributeSelectedLinks', array(
                        'service' => $link['service'],
                        'serviceInfo' => $link['serviceInfo'],
                        'avID' => $avID
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
                $topics = $this->connection->fetchAll('select * from atSocialLinks where avID = ?', [$legacyAVID]);
                foreach($topics as $topic) {
                    $this->connection->insert('TopicAttributeSelectedTopics', array(
                        'treeNodeID' => $topic['TopicNodeID'],
                        'avID' => $avID
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
                'type' => $type
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
            switch($row['atHandle']) {
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
                            foreach($options as $option) {
                                $this->connection->insert('SelectAttributeValueOptions', [
                                    'isEndUserAdded' => $option['isEndUserAdded'],
                                    'displayOrder' => $option['displayOrder'],
                                    'value' => $option['value'],
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
        $this->connection->executeQuery('update AttributeTypes set pkgID = null where pkgID = 0');

        $types = array(
            'express' => 'Express Entity',
            'email' => 'Email Address',
            'telephone' => 'Telephone',
            'url' => 'URL',
        );
        $categories = array('file', 'user', 'collection');
        foreach($types as $handle => $name) {
            $type = Type::getByHandle($handle);
            if (!is_object($type)) {
                $type = Type::add($handle, $name);
                foreach($categories as $category) {
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
            $sp->update(array('cName' => 'Data Objects', 'cDescription' => 'Express Data Objects'));
        }
        $page = Page::getByPath('/dashboard/express/entries');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/express/entries');
            $sp->update(array('cName' => 'View Entries'));
        }
        $page = Page::getByPath('/dashboard/system/express');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express');
            $sp->update(array('cName' => 'Express Data Objects'));
        }
        $page = Page::getByPath('/dashboard/system/express/entities');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/express/entities');
            $sp->update(array('cName' => 'Express Data Types'));
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
    }

    protected function addBlockTypes()
    {
        $bt = BlockType::getByHandle('express_form');
        if (!is_object($bt)) {
            BlockType::installBlockType('express_form');
        }
    }

    protected function addTreeNodeTypes()
    {
        $this->connection->Execute('update TreeNodeTypes set treeNodeTypeHandle = ? where treeNodeTypeHandle = ?', array(
            'category', 'topic_category'
        ));
        $this->connection->Execute('update PermissionKeys set pkHandle = ? where pkHandle = ?', array(
            'view_category_tree_node', 'view_topic_category_tree_node'
        ));
        $this->connection->Execute('update PermissionKeyCategories set pkCategoryHandle = ? where pkCategoryHandle = ?', array(
            'category_tree_node', 'topic_category_tree_node'
        ));
        $results = NodeType::getByHandle('express_entry_results');
        if (!is_object($results)) {
            NodeType::add('express_entry_results');
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

        $desktop = Page::getByPath('/desktop');
        if (!is_object($desktop) || $desktop->isError()) {
            $desktop = SinglePage::add('/desktop');
            $desktop->update(array('cName' => 'Welcome Back'));
            $desktop->setAttribute('desktop_priority', 1);
            $desktop->setAttribute('exclude_nav', true);
            $desktop->setAttribute('is_desktop', true);
            $desktop->setCanonicalPagePath('/account/welcome', false);

            $bt = BlockType::getByHandle("desktop_waiting_for_me");
            $desktop->addBlock($bt, 'Main', array());
        }


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

    public function up(Schema $schema)
    {
        $this->connection->Execute('set foreign_key_checks = 0');
        $this->renameProblematicTables();
        $this->updateDoctrineXmlTables();
        $this->installEntities();
        $this->importAttributeTypes();
        $this->importAttributeKeys();
        $this->addDashboard();
        $this->addBlockTypes();
        $this->updateWorkflows();
        $this->addTreeNodeTypes();
        $this->installDesktops();
        $this->connection->Execute('set foreign_key_checks = 1');

    }

    public function down(Schema $schema)
    {
    }
}

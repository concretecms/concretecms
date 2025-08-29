<?php

declare(strict_types=1);

namespace Concrete\Tests\Attribute;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\Permission\Access\Entity\Type AS PAEType;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Group as GroupType;
use Concrete\Core\Tree\Type\Topic as TopicService;
use Concrete\Core\Tree\Node\Type\Group as GroupNode;
use Concrete\Core\User\Group\Command\AddGroupCommand;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\TestHelpers\Page\PageTestCase;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use SimpleXMLElement;

class ImportExportTest extends PageTestCase
{
    /**
     * Set this constant to true when writing test CIF files.
     *
     * @var bool
     */
    private const NORMALIZE_INPUT_CIF = false;

    /**
     * @var string
     */
    private const ATTRIBUTEKEY_CATEGORY_HANDLE = 'collection';

    /**
     * @var \Concrete\Core\Attribute\Category\AbstractCategory
     */
    private static $categoryEntity;

    /**
     * @var \Concrete\Core\Attribute\ObjectInterface
     */
    private static $attributeOwner;

    /**
     * @var \Concrete\Core\File\Service\VolatileDirectory
     */
    private static $storageVolatileDirectory;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'FileSets',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'TopicTrees',
            'TreeFileFolderNodes',
            'TreeFileNodes',
            'TreeGroupNodes',
            'TreeNodePermissionAssignments',
            'TreeNodes',
            'TreeNodeTypes',
            'Trees',
            'TreeTypes',
            'UserGroups',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(
            parent::getEntityClassNames(),
            [
                Entity\Attribute\Category::class,
                Entity\Attribute\Key\ExpressKey::class,
                Entity\Attribute\Key\FileKey::class,
                Entity\Attribute\Type::class,
                Entity\Attribute\Value\ExpressValue::class,
                Entity\Attribute\Value\FileValue::class,
                Entity\Calendar\Calendar::class,
                Entity\Calendar\CalendarEvent::class,
                Entity\Calendar\CalendarEventOccurrence::class,
                Entity\Calendar\CalendarEventRepetition::class,
                Entity\Calendar\CalendarEventVersion::class,
                Entity\Calendar\CalendarEventVersionOccurrence::class,
                Entity\Express\Entity::class,
                Entity\Express\Entry::class,
                Entity\File\File::class,
                Entity\File\Image\Thumbnail\Type\Type::class,
                Entity\File\StorageLocation\StorageLocation::class,
                Entity\File\StorageLocation\Type\Type::class,
                Entity\File\Version::class,
            ],
            self::listAttributeEntities()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Page\PageTestCase::setupBeforeClass()
     */
    public static function setupBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::createPermissions();
        self::createTrees();
        self::createPages();
        self::createAttributeOwner();
        self::createUsers();
        self::createTopics();
        self::createFiles();
        self::createCalendars();
        self::createExpressEntities();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::tearDownAfterClass()
     */
    public static function tearDownAfterClass(): void
    {
        parent::TearDownAfterClass();
        self::$storageVolatileDirectory = null;
    }

    public function provideCIFCases(): array
    {
        static $cases;
        if ($cases === null) {
            $fs = new FileSystem();
            $cases = [];
            foreach ($fs->directories(DIR_TESTS . '/assets/Attribute/cif') as $attributeTypeDirectory) {
                $attributeTypeHandle = basename($attributeTypeDirectory);
                foreach ($fs->allFiles($attributeTypeDirectory) as $file) {
                    if (strcasecmp($file->getExtension(), 'xml') === 0) {
                        $basename = preg_replace('/\.(out|options)$/', '', $file->getBasename('.xml'));
                    } elseif (strcasecmp($file->getExtension(), 'json') === 0) {
                        $basename = $file->getBasename('.json');
                    } else {
                        continue;
                    }
                    $key = "{$basename}@{$attributeTypeHandle}";
                    if (isset($cases[$key])) {
                        continue;
                    }
                    $options = [];
                    $jsonFile = $file->getPath() . '/' . $basename . '.json';
                    if ($fs->isFile($jsonFile)) {
                        $json = $fs->get($jsonFile);
                        $options = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    }
                    $cases[$key] = [$attributeTypeHandle, $basename, $options];
                }
            }
            $cases = array_values($cases);
        }

        return $cases;
    }

    /**
     * @dataProvider provideCIFCases
     */
    public function testCIFImportExport(string $attributeTypeHandle, string $basename, array $options): void
    {
        if ($options['skipSilent'] ?? false === true) {
            // We have to make at least one assertion, otherwise PHPUnit warns that
            // "This test did not perform any assertions"
            $this->assertTrue(true);
            return;
        }
        if (isset($options['skipReason'])) {
            $this->markTestSkipped($options['skipReason']);
        }
        [$inputSimpleXml, $expectedSimpleXml] = $this->loadTestXml($attributeTypeHandle, $basename, $options);
        $key = $this->createAttributeKey($attributeTypeHandle, $basename);
        $keyHandle = $key->getAttributeKeyHandle();
        $keyController = $key->getController();
        $importedValue = $keyController->importValue($inputSimpleXml);
        $value = self::$attributeOwner->setAttribute($key, $importedValue, false);
        $this->assertInstanceOf(AttributeValueInterface::class, $value);

        $em = app(EntityManagerInterface::class);
        $em->clear();

        $key2 = self::$attributeOwner->getObjectAttributeCategory()->getAttributeKeyByHandle($keyHandle);
        $value2 = self::$attributeOwner->getAttributeValueObject($key2, false);

        if (isset($options['richTexts'])) {
            foreach ($options['richTexts'] as $query => $info) {
                $this->checkRichText($value2, $query, $info);
            }
        }

        $keyController2 = $key2->getController();
        $keyController2->setAttributeValue($value2);
        $actualSimpleXml = simplexml_load_string('<attributekey />');
        $keyController2->exportValue($actualSimpleXml);

        $this->assertSameXml($expectedSimpleXml->asXML(), $actualSimpleXml->asXML(), $options['keepXmlElementsOrder'] ?? false);
    }

    /**
     * @return \SimpleXMLElement[]
     */
    private function loadTestXml(string $attributeTypeHandle, string $basename, array $options): array
    {
        $baseFullname = DIR_TESTS . "/assets/Attribute/cif/{$attributeTypeHandle}/{$basename}";
        $inFullname = "{$baseFullname}.xml";
        $outFullname = "{$baseFullname}.out.xml";
        if (!is_file($outFullname)) {
            $outFullname = $inFullname;
        }
        if (empty($options['keepXmlElementsOrder'])) {
            $inSimpleXml = $this->loadNormalizedInputCif($inFullname);
            $outSimpleXml = $this->loadNormalizedInputCif($outFullname);
        } else {
            $inSimpleXml = simplexml_load_file($inFullname);
            $this->assertInstanceOf(SimpleXMLElement::class, $inSimpleXml);
            $outSimpleXml = simplexml_load_file($outFullname);
            $this->assertInstanceOf(SimpleXMLElement::class, $outSimpleXml);
        }

        return [$inSimpleXml, $outSimpleXml];
    }

    private function loadNormalizedInputCif(string $cifFile): SimpleXMLElement
    {
        $xml = file_get_contents($cifFile);
        $this->assertNotSame(false, $xml, "Failed to load file {$cifFile}");
        $normalizedXml = $this->normalizeXml($xml, false);
        if (self::NORMALIZE_INPUT_CIF) {
            if ($normalizedXml !== $xml) {
                $this->assertNotSame(false, file_put_contents($cifFile, $normalizedXml), "Failed to update file {$cifFile}");
            }
        } else {
            $this->assertSame($normalizedXml, $xml, "Please update the file {$cifFile} with the following changes (or set the NORMALIZE_INPUT_CIF constant to true)");
        }
        $sx = simplexml_load_string($normalizedXml);

        return $sx;
    }


    public function testAttributeTypeCoverage(): void
    {
        $fs = new Filesystem();
        $availableHandles = array_map('basename', $fs->directories(DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES));
        $coveredHandles = array_unique(
            array_map(
                static function (array $case): string {
                    return $case[0];
                },
                $this->provideCIFCases()
            )
        );
        $this->assertSame([], array_values(array_diff($availableHandles, $coveredHandles)), 'Found attribute types lacking tests');
    }

    private function assertSameXml(string $expected, string $actual, bool $keepXmlElementsOrder): void
    {
        $expected = $this->normalizeXml($expected, false);
        $actual = $this->normalizeXml($actual, $keepXmlElementsOrder);

        $this->assertSame($expected, $actual);
    }

    private function normalizeXml(string $xml, bool $keepXmlElementsOrder): string
    {
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xml);
        $xpath = new DOMXPath($doc);
        // Let's expand all the CDATA elements
        $cdatas = $xpath->query('//text()[ancestor-or-self::*][self::node()[local-name()=""]]');
        foreach ($cdatas as $cdata) {
            $textNode = $doc->createTextNode($cdata->data);
            $cdata->parentNode->replaceChild($textNode, $cdata);
        }
        // Let's use CDATA for elements containing &, < or >
        $elementsWithoutChildElements = $xpath->query('//*[not(*)]');
        foreach ($elementsWithoutChildElements as $elementWithoutChildElements) {
            if (!$elementWithoutChildElements->hasChildNodes()) {
                continue;
            }
            if ($elementWithoutChildElements->childNodes->length !== 1) {
                continue;
            }
            $childNode = $elementWithoutChildElements->childNodes->item(0);
            if ($childNode->nodeType !== XML_TEXT_NODE) {
                continue;
            }
            $text = $childNode->nodeValue;
            if (strpbrk($text, "&<>") === false) {
                continue;
            }
            $cdata = $doc->createCDATASection($text);
            $elementWithoutChildElements->replaceChild($cdata, $childNode);
        }
        if (!$keepXmlElementsOrder) {
            // Let's sort elements alphabetically (CIF usually doesn't rely on elements order)
            $this->sortXmlChildElements($doc->documentElement);
        }

        return $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
    }

    private function sortXmlChildElements(DOMElement $parentElement): void
    {
        $allChildElements = array_filter(
            iterator_to_array($parentElement->childNodes),
            static function (DOMNode $childNode) use ($parentElement): bool {
                return $childNode instanceof DOMElement;
            }
        );
        $childElementsToBeSorted = array_filter(
            $allChildElements,
            static function (DOMElement $childElement) use ($parentElement): bool {
                if ($parentElement->tagName === 'block' && $childElement->tagName === 'data' && $childElement->hasAttribute('table')) {
                    return false;
                }
                return true;
            }
        );
        $elementsByName = [];
        foreach ($childElementsToBeSorted as $childElement) {
            if (isset($elementsByName[$childElement->tagName])) {
                $elementsByName[$childElement->tagName][] = $childElement;
            } else {
                $elementsByName[$childElement->tagName] = [$childElement];
            }
        }
        ksort($elementsByName, SORT_NATURAL);
        foreach ($elementsByName as $elements) {
            foreach ($elements as $element) {
                $parentElement->removeChild($element);
                $parentElement->appendChild($element);
            }
        }
        foreach ($allChildElements as $childElement) {
            $this->sortXmlChildElements($childElement);
        }
    }

    private static function createPermissions(): void
    {
        if (!PAEType::getByHandle('group')) {
            PAEType::add('group', 'Group');
        }
    }

    private static function createTrees(): void
    {
        if (TreeNodeType::getByHandle('category') === null) {
            TreeNodeType::add('category');
        }
        if (TreeType::getByHandle('topic') === null) {
            TreeType::add('topic');
        }
        if (TreeNodeType::getByHandle('topic') === null) {
            TreeNodeType::add('topic');
        }
        if (TreeType::getByHandle('group') === null) {
            TreeType::add('group');
        }
        if (TreeNodeType::getByHandle('group') === null) {
            TreeNodeType::add('group');
        }
        GroupType::add();
    }

    private static function createPages(): void
    {
        static::createPage('Test Page 1');
        static::createPage('Test Page 2');
        static::createPage('Test Page 3');
    }

    private static function createAttributeOwner(): void
    {
        $categoryService = app(CategoryService::class);
        if ($categoryService->getByHandle(self::ATTRIBUTEKEY_CATEGORY_HANDLE) === null) {
            $categoryService->add(self::ATTRIBUTEKEY_CATEGORY_HANDLE);
        }
        $page = static::createPage('Test Attributes Owner');
        self::$attributeOwner = $page->getVersionToModify()->getVersionObject();
    }

    private static function createUsers(): void
    {
        $groupRepository = app(GroupRepository::class);
        if ($groupRepository->getGroupById(GUEST_GROUP_ID) === null) {
            $command = new AddGroupCommand();
            $command->setName('Guest');
            $command->setDescription('Guests');
            $command->getForcedNewGroupID(GUEST_GROUP_ID);
            app()->executeCommand($command);
        }
        for ($i = 1; $i <= 3; $i++) {
            $command = new AddGroupCommand();
            $command->setName("Sample User Group #{$i}");
            $command->setDescription('This is a test user group');
            $parentGroup = app()->executeCommand($command);
            $parentGroupNode = GroupNode::getTreeNodeByGroupID($parentGroup->getGroupID());
            $command = new AddGroupCommand();
            $command->setName('Child Group Name');
            $command->setDescription('Group at 2nd level');
            $command->setParentNodeID((int) $parentGroupNode->getTreeNodeID());
            app()->executeCommand($command);
        }
        $registrationService = app('user/registration');
        $registrationService->create([
            'uName' => USER_SUPER,
            'uPassword' => '12345',
            'uEmail' => 'admin@example.com',
            'uDefaultLanguage' => 'en_US',
            'uHomeFileManagerFolderID' => null,
        ]);
        $registrationService->create([
            'uName' => 'jane_doe',
            'uPassword' => 'ABCDE',
            'uEmail' => 'jane@doe.org',
            'uDefaultLanguage' => 'en_US',
            'uHomeFileManagerFolderID' => null,
        ]);
        $registrationService->create([
            'uName' => 'john_doe',
            'uPassword' => 'FGHIJ',
            'uEmail' => 'john@doe.org',
            'uDefaultLanguage' => 'en_US',
            'uHomeFileManagerFolderID' => null,
        ]);
    }

    private static function createTopics(): void
    {
        $topicsTree = TopicService::add('Test Topic Tree');
        $root = $topicsTree->getRootTreeNodeObject();
        $child = TopicTreeNode::add('Parent #1', $root);
        TopicTreeNode::add('Child #1.1', $child);
        $child = TopicTreeNode::add('Parent #2', $root);
        TopicTreeNode::add('Child #2.1', $child);
        TopicTreeNode::add('Child #2.2', $child);
        TopicTreeNode::add('Child #2.3', $child);
    }

    private static function createFiles(): void
    {
        self::$storageVolatileDirectory = app(VolatileDirectory::class, ['parentDirectory' => sys_get_temp_dir()]);
        $storageLocationType = StorageLocationType::add('local', 'Local Storage');
        $storageLocationConfiguration = $storageLocationType->getConfigurationObject();
        $storageLocationConfiguration->setRootPath(self::$storageVolatileDirectory->getPath());
        $storageLocationConfiguration->setWebRootRelativePath('/application/files');
        $storageLocationFactory = app(StorageLocationFactory::class);
        $storageLocation = $storageLocationFactory->create($storageLocationConfiguration, 'Default');
        $storageLocation->setIsDefault(true);
        $storageLocationFactory->persist($storageLocation);
        $fileManager = app(\Concrete\Core\File\Filesystem::class)->create();
        $rootFileFolder = $fileManager->getRootTreeNodeObject();
        $importer = app(FileImporter::class);
        $importOptions = app(ImportOptions::class)
            ->setCanChangeLocalFile(false)
        ;
        $importOptions->setCustomPrefix('123456789012');
        $importer->importLocalFile(DIR_TESTS . '/assets/Attribute/cif/test.jpg', 'file-1.jpg', $importOptions);
        $importOptions->setCustomPrefix('210987654321');
        $importer->importLocalFile(DIR_TESTS . '/assets/Attribute/cif/test.jpg', 'file-2.jpg', $importOptions);
        $importOptions->setCustomPrefix('123456543210');
        $importer->importLocalFile(DIR_TESTS . '/assets/Attribute/cif/test.jpg', 'file-3.jpg', $importOptions);
        FileSet::create('Test File Set #1');
        FileSet::create('Test File Set #2');
        FileSet::create('Test File Set #3');
        $rootFileFolder->add('Sample File Folder #1', $rootFileFolder);
        $folder = $rootFileFolder->add('Sample File Folder #2', $rootFileFolder);
        $rootFileFolder->add('Child Folder', $folder);
        $rootFileFolder->add('Sample File Folder #3', $rootFileFolder);
    }

    private static function createCalendars(): void
    {
        $em = app(EntityManagerInterface::class);
        $user = $em->find(Entity\User\User::class, USER_SUPER_ID);
        $calendar = new Entity\Calendar\Calendar();
        $calendar->setName('Calendar Name #1');
        $em->persist($calendar);
        $calendar = new Entity\Calendar\Calendar();
        $calendar->setName('Calendar Name #2');
        $em->persist($calendar);
        foreach ([
            ['name' => 'Event Name #2.1', 'startTimestamp' => strtotime('2025-01-01T00:00:01+00:00'), 'endTimestamp' => strtotime('2025-06-30T12:13:14+00:00')],
            ['name' => 'Event Name #2.2', 'startTimestamp' => strtotime('2025-12-31T23:59:59+00:00'), 'endTimestamp' => strtotime('2026-01-01T00:00:01+00:00')],
        ] as $eventInfo) {
            $calendarEvent = new Entity\Calendar\CalendarEvent($calendar);
            $calendarEventVersion = new Entity\Calendar\CalendarEventVersion($calendarEvent, $user);
            $calendarEventVersion->setEvent($calendarEvent);
            $calendarEventVersion->setName($eventInfo['name']);
            $calendarEventVersion->setIsApproved(1);
            $calendarEventRepetitionObject = new EventRepetition();
            $calendarEventRepetitionObject->setStartDateAllDay(0);
            $calendarEventRepetitionObject->setRepeatPeriod(EventRepetition::REPEAT_NONE);
            $calendarEventRepetition = new Entity\Calendar\CalendarEventRepetition($calendarEventRepetitionObject);
            $calendarEventVersionOccurrence = new Entity\Calendar\CalendarEventVersionOccurrence(
                $calendarEventVersion,
                $calendarEventRepetition,
                $eventInfo['startTimestamp'],
                $eventInfo['endTimestamp']
            );
            $calendarEventVersion->getOccurrences()->add($calendarEventVersionOccurrence);
            $em->persist($calendarEvent);
            $em->persist($calendarEventVersion);
            $em->persist($calendarEventRepetition);
            $em->persist($calendarEventVersionOccurrence);
        }
        $em->flush();
        $em->clear(); // @todo remove this line when collections are managed correctly
    }

    private static function getOrCreateAttributeType(string $attributeTypeHandle): Entity\Attribute\Type
    {
        $typeFactory = app(TypeFactory::class);
        $type = $typeFactory->getByHandle($attributeTypeHandle);
        if ($type === null) {
            $type = $typeFactory->add($attributeTypeHandle, "Name of {$attributeTypeHandle} attribute type");
        }

        return $type;
    }

    private function createAttributeKey(string $attributeTypeHandle, string $basename): Entity\Attribute\Key\Key
    {
        static $keyIndex;

        $keyIndex = ($keyIndex ?? 0) + 1;

        $type = self::getOrCreateAttributeType($attributeTypeHandle);

        $optionsFullname = DIR_TESTS . "/assets/Attribute/cif/{$attributeTypeHandle}/{$basename}.options.xml";
        if (is_file($optionsFullname)) {
            $optionsSimpleXml = simplexml_load_file($optionsFullname);
        } else {
            $optionsSimpleXml = simplexml_load_string('<attributekey />');
        }
        $this->assertInstanceOf(SimpleXMLElement::class, $optionsSimpleXml);
        $this->assertSame('attributekey', $optionsSimpleXml->getName());
        $optionsSimpleXml['category'] = self::ATTRIBUTEKEY_CATEGORY_HANDLE;
        $optionsSimpleXml['type'] = $type->getAttributeTypeHandle();
        $optionsSimpleXml['handle'] = "{$attributeTypeHandle}_key_{$keyIndex}";
        $optionsSimpleXml['name'] = "Key of type {$attributeTypeHandle} ({$keyIndex})";
        $optionsSimpleXml['searchable'] = '0';
        $optionsSimpleXml['indexed'] = '0';
        $optionsSimpleXml['internal'] = '0';
        $categoryService = app(CategoryService::class);
        $categoryEntity = $categoryService->getByHandle(self::ATTRIBUTEKEY_CATEGORY_HANDLE);
        $categoryController = $categoryEntity->getController();
        $key = $categoryController->import($type, $optionsSimpleXml);

        return $key;
    }

    private function checkRichText(\Concrete\Core\Entity\Attribute\Value\AbstractValue $value, string $query, array $info): void
    {
        $richText = (string) app(Connection::class)->fetchOne($query, ['avID' => $value->getAttributeValueID()]);

        $pattern = '#{CCM:FID_DL_(?i:[0-9A-F][0-9A-F\-]+[0-9A-F])}#';
        $expectedNum = $info['numFiles'] ?? 0;
        $actualNum = preg_match_all($pattern, $richText);
        $this->assertSame($expectedNum, $actualNum, "The rich text\n{$richText}\ncontain references to {$actualNum} file(s) instead of {$expectedNum}.\nPS: pattern used: {$pattern}\n");

        $pattern = '/<concrete-picture\s[^>]*(?i:\bfid)\s*=\s*(?:([1-9]\d*)|"([1-9]\d*)"|\'([1-9]\d*)\')[\s>]/';
        $expectedNum = $info['numImages'] ?? 0;
        $actualNum = preg_match_all($pattern, $richText);
        $this->assertSame($expectedNum, $actualNum, "The rich text\n{$richText}\ncontain references to {$actualNum} image(s) instead of {$expectedNum}.\nPS: pattern used: {$pattern}\n");

        $pattern = '#{CCM:CID_[1-9]\d*}#';
        $expectedNum = $info['numPages'] ?? 0;
        $actualNum = preg_match_all($pattern, $richText);
        $this->assertSame($expectedNum, $actualNum, "The rich text\n{$richText}\ncontain references to {$actualNum} pages(s) instead of {$expectedNum}.\nPS: pattern used: {$pattern}\n");
    }

    /**
     * @return string[]
     */
    private static function listAttributeEntities(): array
    {
        return array_merge(
            self::listAttributeEntitiesWith(['Attribute', 'Key', 'Settings']),
            self::listAttributeEntitiesWith(['Attribute', 'Value', 'Value']),
        );
    }

    /**
     * @param string[] $parts
     *
     * @return string[]
     */
    private static function listAttributeEntitiesWith(array $parts): array
    {
        $result = [];
        $namespacePrefix = 'Concrete\\Core\\Entity\\' . implode('\\', $parts) . '\\';
        $directory = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES . '/' . implode('//', $parts) . '/';
        $fs = new FileSystem();
        foreach ($fs->allFiles($directory) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $absolutePath = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());
            if (!str_starts_with($absolutePath, $directory)) {
                continue;
            }
            $relativePath = substr($absolutePath, strlen($directory));
            $className = $namespacePrefix . str_replace('/', '\\', preg_replace('/\.php/i', '', $relativePath));
            if (!class_exists($className)) {
                continue;
            }
            $class = new ReflectionClass($className);
            if ($class->isAbstract()) {
                continue;
            }
            $phpDoc = $class->getDocComment();
            if (!$phpDoc || !preg_match('/\\\\Entity\b/i', $phpDoc) || !preg_match('/\\\\Table\b/i', $phpDoc)) {
                continue;
            }
            $result[] = $className;
        }

        return $result;
    }

    private static function createExpressEntities(): void
    {
        $categoryService = app(CategoryService::class);
        if (($categoryService->getByHandle('express')) === null) {
            $categoryService->add('express');
        }
        $em = app(EntityManagerInterface::class);
        $labelAttributeType = self::getOrCreateAttributeType('text');

        $createLabelAttribute = static function (Entity\Express\Entity $entity) use ($labelAttributeType): Entity\Attribute\Key\ExpressKey
        {
            static $keyIndex;
            $keyIndex = ($keyIndex ?? 0) + 1;
            $category = $entity->getAttributeKeyCategory();
            /** @var \Concrete\Core\Attribute\Category\ExpressCategory $category */
            $key = $category->createAttributeKey();
            $key->setAttributeKeyName("Express Label {$keyIndex}");
            $key->setAttributeKeyHandle("express_label_{$keyIndex}");
            $key->setIsAttributeKeySearchable(false);
            $controller = $labelAttributeType->getController();
            $controller->setAttributeKey($key);
            $settings = $controller->createAttributeKeySettings();
            $expressKey = $category->add($labelAttributeType, $key, $settings);
            $expressKey->setEntity($entity);
            $entity->getAttributes()->add($expressKey);

            return $expressKey;
        };

        $createEntry = static function (Entity\Express\Entity $entity, Entity\Attribute\Key\ExpressKey $labelAttribute, string $label) use ($em): Entity\Express\Entry {
            $entry = new Entity\Express\Entry();
            $em->persist($entry);
            $entry->setEntity($entity);
            $entity->getEntries()->add($entry);
            $attribute = $entry->setAttribute($labelAttribute, $label, false);
            $entry->getAttributes()->add($attribute);

            return $entry;
        };

        $em->persist($entity = new Entity\Express\Entity());
        $entity->setName('Example Entity #1');
        $entity->setHandle('example_entity_n1');
        $entity->setPluralHandle('example_entities_n1');
        $entity->setEntityResultsNodeId(0); // ?
        $em->flush();
        $labelAttribute = $createLabelAttribute($entity);
        $createEntry($entity, $labelAttribute, 'Entry #1 of Entity #1');
        $createEntry($entity, $labelAttribute, 'Entry #2 of Entity #1');

        $em->persist($entity = new Entity\Express\Entity());
        $entity->setName('Example Entity #2');
        $entity->setHandle('example_entity_n2');
        $entity->setPluralHandle('example_entities_n2');
        $entity->setEntityResultsNodeId(0); // ?
        $em->flush();
        $labelAttribute = $createLabelAttribute($entity);
        $createEntry($entity, $labelAttribute, 'Entry #1 of Entity #2');
        $createEntry($entity, $labelAttribute, 'Entry #2 of Entity #2');

        $em->flush();
    }
}

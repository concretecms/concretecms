<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Attribute\Category\CategoryService as AttributeCategoryService;
use Concrete\Core\Attribute\TypeFactory as AttributeTypeFactory;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Entity\Page\Feed as FeedEntity;
use Concrete\Core\Entity\Sharing\SocialNetwork\Link as SocialLink;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Stack\Folder\FolderService as StackFolderService;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as ComposerControlType;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Access\Entity\Type AS PAEType;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Topic as TopicService;
use Concrete\Core\User\Group\Command\AddGroupCommand;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\TestHelpers\Page\PageTestCase;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Filesystem\Filesystem;
use Mockery as M;
use SimpleXMLElement;

class ImportExportTest extends PageTestCase
{
    /**
     * Set this constant to true when writing test CIF files.
     *
     * @var bool
     */
    private const NORMALIZE_INPUT_CIF = true;

    /**
     * @var \Concrete\Core\File\Service\VolatileDirectory
     */
    private static $storageVolatileDirectory;

    /**
     * @var \Concrete\Core\Page\Page
     */
    private static $blockPage;

    /**
     * @var \Concrete\Core\Tree\Type\Topic
     */
    private static $topicsTree;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'AreaLayouts',
            'AreaLayoutColumns',
            'AreaLayoutPresets',
            'AreaLayoutThemeGridColumns',
            'AreaPermissionAssignments',
            'Blocks',
            'BlockTypeSets',
            'Conversations',
            'ConversationSubscriptions',
            'FileSets',
            'PageTypeComposerControlTypes',
            'PageTypeComposerFormLayoutSetControls',
            'PageTypeComposerOutputControls',
            'PageTypePageTemplateDefaultPages',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'Stacks',
            'TopicTrees',
            'TreeTypes',
            'Trees',
            'TreeFileFolderNodes',
            'TreeNodeTypes',
            'TreeNodes',
            'TreeNodePermissionAssignments',
            'TreeFileNodes',
            'TreeGroupNodes',
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
        return array_merge(parent::getEntityClassNames(), [
            Entity\Attribute\Category::class,
            Entity\Attribute\Key\FileKey::class,
            Entity\Attribute\Type::class,
            Entity\Attribute\Value\FileValue::class,
            Entity\Board\Board::class,
            Entity\Board\InstanceLog::class,
            Entity\Block\BlockType\BlockType::class,
            Entity\Calendar\Calendar::class,
            Entity\File\File::class,
            Entity\File\Image\Thumbnail\Type\Type::class,
            Entity\File\Image\Thumbnail\Type\TypeFileSet::class,
            Entity\File\StorageLocation\StorageLocation::class,
            Entity\File\StorageLocation\Type\Type::class,
            Entity\File\Version::class,
            Entity\Page\Container::class,
            Entity\Attribute\Value\Value\TopicsValue::class,
            Entity\Attribute\Key\Settings\TopicsSettings::class,
            Entity\Page\Container\Instance::class,
            Entity\Page\Container\InstanceArea::class,
            Entity\Page\Feed::class,
            Entity\Sharing\SocialNetwork\Link::class,
            Entity\Statistics\UsageTracker\FileUsageRecord::class,
            Entity\StyleCustomizer\Inline\StyleSet::class,
        ]);
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
        self::createUsers();
        self::createTopics();
        self::createAttributes();
        self::createThumbnailTypes();
        self::createFiles();
        self::createBoards();
        self::createCalendars();
        self::createContainers();
        self::createStacks();
        self::createSocialLinks();
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
            foreach ($fs->directories(DIR_TESTS . '/assets/Block/cif') as $blockTypeDirectory) {
                $blockTypeHandle = basename($blockTypeDirectory);
                foreach ($fs->allFiles($blockTypeDirectory) as $file) {
                    if (strcasecmp($file->getExtension(), 'xml') !== 0) {
                        continue;
                    }
                    $options = [];
                    $jsonFile = $file->getPath() . '/' . $file->getBasename('.xml') . '.json';
                    if ($fs->isFile($jsonFile)) {
                        $json = $fs->get($jsonFile);
                        $options = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    }
                    $cases[] = [
                        $blockTypeHandle,
                        str_replace(DIRECTORY_SEPARATOR, '/', $file->getBasename('.xml')),
                        $options,
                    ];
                }
            }
        }

        return $cases;
    }

    public function provideBlocksWithRichText(): array
    {
        $result = [];
        foreach ($this->provideCIFCases() as [$blockTypeHandle,, $options]) {
            if (in_array([$blockTypeHandle], $result, true)) {
                continue;
            }
            if (($options['richTexts'] ?? []) === []) {
                continue;
            }
            $result[] = [$blockTypeHandle];
        }

        return $result;
    }

    /**
     * @dataProvider provideCIFCases
     */
    public function testCIFImportExport(string $blockTypeHandle, string $cifBaseFileName, array $options): void
    {
        if (isset($options['skipReason'])) {
            $this->markTestSkipped($options['skipReason']);
        }
        $cifFile = DIR_TESTS . "/assets/Block/cif/{$blockTypeHandle}/{$cifBaseFileName}.xml";
        if (empty($options['keepXmlElementsOrder'])) {
            $inputCif = $this->loadNormalizedInputCif($cifFile);
        } else {
            $inputCif = simplexml_load_file($cifFile);
            $this->assertInstanceOf(SimpleXMLElement::class, $inputCif);
        }
        foreach (($options['requiredBlockTypes'] ?? []) as $requiredBlockTypeHandle) {
            if (BlockType::getByHandle($requiredBlockTypeHandle)) {
                continue;
            }
            $requiredBlockType = BlockType::installBlockType($requiredBlockTypeHandle);
            $this->assertInstanceOf(BlockTypeEntity::class, $requiredBlockType);
        }
        $blockType = BlockType::getByHandle($blockTypeHandle) ?: BlockType::installBlockType($blockTypeHandle);
        $this->assertInstanceOf(BlockTypeEntity::class, $blockType);
        $importerExporterMethod = $options['importerExporterMethod'] ?? 'importExportBlockType';
        $this->assertTrue(method_exists($this, $importerExporterMethod), "The method '{$importerExporterMethod}' specified in the options does not exist");
        $outputCif = $this->{$importerExporterMethod}($blockType, $inputCif, $options);
        $this->assertSameXML($inputCif->asXML(), $outputCif, $options['keepXmlElementsOrder'] ?? false);
    }

    /**
     * @dataProvider provideBlocksWithRichText
     */
    public function testProvideContents(string $blockTypeHandle): void
    {
        $blockType = BlockType::getByHandle($blockTypeHandle);
        if (!$blockType) {
            $blockType = BlockType::installBlockType($blockTypeHandle);
        }
        $expectedMethod = 'getSearchableContent';
        $controllerClass = ltrim($blockType->getBlockTypeClass(), '\\');
        $this->assertTrue(
            method_exists($controllerClass, $expectedMethod),
            "Since the block type with handle {$blockTypeHandle} uses rich text, its controller ({$controllerClass}) should implement the {$expectedMethod}() method");
    }

    /**
     * @dataProvider provideBlocksWithRichText
     */
    public function testImplementsFileTrackableInterface(string $blockTypeHandle): void
    {
        $blockType = BlockType::getByHandle($blockTypeHandle);
        if (!$blockType) {
            $blockType = BlockType::installBlockType($blockTypeHandle);
        }
        $expectedInterface = FileTrackableInterface::class;
        $controllerClass = ltrim($blockType->getBlockTypeClass(), '\\');
        $implementedInterfaces = class_implements($controllerClass);
        $this->assertTrue(
            in_array($expectedInterface, $implementedInterfaces, true),
            "Since the block type with handle {$blockTypeHandle} uses rich text, its controller ({$controllerClass}) should implement the {$expectedInterface} interface"
        );
    }

    private function loadNormalizedInputCif(string $cifFile): SimpleXMLElement
    {
        $xml = file_get_contents($cifFile);
        $this->assertNotSame(false, $xml, "Failed to load file {$cifFile}");
        $normalizedXml = $this->normalizeXML($xml, false);
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

    private function importExportBlockType(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options, &$createdBlock = null): string
    {
        $blockType->loadController();
        $blockController = $blockType->getController();
        $this->assertInstanceOf(BlockController::class, $blockController);
        $createdBlock = $blockController->import(self::$blockPage, 'Main', $inputCif);
        $this->assertInstanceOf(Block::class, $createdBlock);
        $this->checkFileUsageCount($createdBlock, $options['fileUsageCount'] ?? 0);
        if (isset($options['richTexts'])) {
            foreach ($options['richTexts'] as $query => $info) {
                $this->checkRichText($createdBlock->getBlockID(), $query, $info);
            }
        }
        $outputCif = simplexml_load_string('<root />');
        $createdBlock->export($outputCif);
        $this->assertTrue(isset($outputCif->block));

        return $outputCif->block->asXML();
    }

    private function importExportPageType1(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        if (!ComposerControlType::getByHandle('block')) {
            ComposerControlType::add('block', 'Block');
        }
        PageType::import($inputCif);
        PageType::importContent($inputCif);
        $importedPageType = PageType::getByHandle('test_page_type');
        $this->assertInstanceOf(PageType::class, $importedPageType);
        $outputCif = simplexml_load_string('<root />');
        $importedPageType->export($outputCif);
        $this->assertTrue(isset($outputCif->pagetype));
        $pageNode = $outputCif->pagetype[0]->composer[0]->output[0]->pagetemplate[0]->page[0];
        $blockNode = $pageNode->area[0]->blocks[0]->block[0];
        $tempID = (string) $outputCif->pagetype[0]->composer[0]->formlayout[0]->set[0]->control[0]['output-control-id'];
        $this->assertRegExp('/\w{5,}/', $tempID);
        $this->assertNotSame("CCMTest1", $tempID);
        $this->assertSame($tempID, (string) $blockNode->control[0]['output-control-id']);
        unset($blockNode['mc-block-id']);
        unset($pageNode['user']);
        unset($pageNode['public-date']);
        $xml = $outputCif->pagetype->asXML();
        $xml = strtr($xml, [
            " output-control-id=\"{$tempID}\"" => ' output-control-id="CCMTest1"',
        ]);

        return $xml;
    }

    private function exportCoreScrapbookDisplay1(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        $contentBlockType = BlockType::getByHandle('content');
        $contentBlock = null;
        $this->importExportBlockType($contentBlockType, $inputCif, $options, $contentBlock);
        $aliasBlock = self::$blockPage->addBlock($blockType, 'Main', ['bOriginalID' => $contentBlock->getBlockID()]);
        $outputCif = simplexml_load_string('<root />');
        $aliasBlock->export($outputCif);
        $this->assertTrue(isset($outputCif->block));

        return $outputCif->block->asXML();
    }

    private function importExportExpressEntryDetail(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        return $this->importExportExpress($blockType, $inputCif, $options);
    }

    private function importExportExpressForm(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        return $this->importExportExpress($blockType, $inputCif, $options);
    }

    private function importExportExpress(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        $sampleEntity =  new \Concrete\Core\Entity\Express\Entity();
        $sampleEntity->setId('1cafebab-babe-cafe-babe-1cafebabe1ca');
        $sampleEntity->setHandle('example_entity_n1');
        $sampleForm = new \Concrete\Core\Entity\Express\Form();
        $sampleForm->setId('2cafebab-babe-cafe-babe-2cafebabe2ca');
        $sampleForm->setName('Example Form #1');
        $sampleForm->setEntity($sampleEntity);

        $emOriginal = $this->app->make(\Doctrine\ORM\EntityManager::class);
        $em = M::mock($emOriginal)->makePartial();
        $em->shouldReceive('find')->andReturnUsing(static function($className, $id) use ($emOriginal, $sampleForm) {
            switch ($className) {
                case \Concrete\Core\Entity\Express\Entity::class:
                    switch ($id) {
                        case '1cafebab-babe-cafe-babe-1cafebabe1ca':
                            return $sampleForm->getEntity();
                    }
                    break;
                case \Concrete\Core\Entity\Express\Form::class:
                    switch ($id) {
                        case '2cafebab-babe-cafe-babe-2cafebabe2ca':
                            return $sampleForm;
                    }
                    break;
            }
            return call_user_func_array([$emOriginal, 'find'], func_get_args());
        });
        $this->app->singleton(\Doctrine\ORM\EntityManager::class, static function() use ($em) { return $em; });
        try {
            return $this->importExportBlockType($blockType, $inputCif, $options);
        } finally {
            $this->app->singleton(\Doctrine\ORM\EntityManager::class, static function() use ($emOriginal) { return $emOriginal; });
        }
    }

    private function importExportPageListFeedExisting(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        $em = app(EntityManagerInterface::class);
        $repo = $em->getRepository(FeedEntity::class);
        $feed = $repo->findOneBy(['pfHandle' => 'pagelist-feed-existing']);
        if ($feed) {
            $em->remove($feed);
        }
        $feed = new FeedEntity();
        $feed->setHandle('pagelist-feed-existing');
        $feed->setTitle('Original title');
        $feed->setDescription('Original description');
        $feed->setParentID(0x7FFFFFFF);
        $em->persist($feed);
        $em->flush();
        try {
            $result = $this->importExportBlockType($blockType, $inputCif, $options);
            $feed2 = $repo->findOneBy(['pfHandle' => 'pagelist-feed-existing']);
            $this->assertSame($feed, $feed2);
            $this->assertSame('Title of the Existing Feed', $feed2->getTitle());
            $this->assertSame('Description of the Existing Feed', $feed2->getDescription());
            $this->assertNotSame(0x7FFFFFFF, (int) $feed->getParentID());
        } finally {
            try {
                if (isset($feed)) {
                    $em->remove($feed);
                }
                if (isset($feed2)) {
                    $em->remove($feed2);
                }
                $em->flush();
            } catch (\Throwable $_) {
            }
        }
        return $result;
    }

    private function importExportPageListFeedNew(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        $em = app(EntityManagerInterface::class);
        $repo = $em->getRepository(FeedEntity::class);
        $feed = $repo->findOneBy(['pfHandle' => 'pagelist-feed-new']);
        if ($feed) {
            $em->remove($feed);
            $em->flush();
        }
        try {
            $result = $this->importExportBlockType($blockType, $inputCif, $options);
            $feed = $repo->findOneBy(['pfHandle' => 'pagelist-feed-new']);
            $this->assertNotNull($feed, 'The Page List block type should create an RSS feed');
            $this->assertSame('Title of the New Feed', $feed->getTitle());
            $this->assertSame('Description of the New Feed', $feed->getDescription());
        } finally {
            if (isset($feed)) {
                try {
                    $em->remove($feed);
                    $em->flush();
                } catch (\Throwable $_) {
                }
            }
        }

        return $result;
    }

    private function importExportSurvey(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options): string
    {
        $inputBaseIndex = 1000;
        $cn = $this->app->make(Connection::class);
        $outputBaseIndex = (int) $cn->fetchOne(
            "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'btSurveyOptions'"
        );
        $outputXml = $this->importExportBlockType($blockType, $inputCif, $options);
        $outputXmlForCompare = preg_replace_callback(
            '#<optionID>\s*(?:<!\[CDATA\[\s*)?(?<id>\d+)\s*(?:\]\]>\s*)?</optionID>#',
            static function (array $matches) use ($outputBaseIndex): string {
                return str_replace(
                    $matches['id'],
                    '{{base}} + ' . (1 + ((int) $matches['id']) - $outputBaseIndex),
                    $matches[0]
                );
            },
            $outputXml
        );
        foreach ($inputCif->xpath('//optionID') as $xOptionID) {
            $id = trim((string) $xOptionID[0]);
            if (preg_match('/^\d+/', $id)) {
                $xOptionID[0] = '{{base}} + ' . (((int) $id) - $inputBaseIndex);
            }
        }

        return $outputXmlForCompare;
    }

    public function testBlockTypeCoverage(): void
    {
        $fs = new Filesystem();
        $availableHandles = array_map('basename', $fs->directories(DIR_FILES_BLOCK_TYPES_CORE));
        $coveredHandles = array_unique(
            array_map(
                static function (array $case): string {
                    return $case[0];
                },
                $this->provideCIFCases()
            )
        );
        $expectedUncoveredHandles = [
            'core_board_slot', // does it make sense to test it?
            'form', // old stuff that's not worth working on
            'tags',
            'testimonial',
            'topic_list',
            'top_navigation_bar',
            'video',
            'youtube',
        ];
        $this->assertSame([], array_diff($expectedUncoveredHandles, $availableHandles), 'Found unknown block types marked as lacking tests');
        $this->assertSame([], array_intersect($coveredHandles, $expectedUncoveredHandles), 'Found block types having tests but marked as lacking tests');
        $this->assertSame([], array_diff($availableHandles, $coveredHandles, $expectedUncoveredHandles), 'Found block types lacking tests');
    }

    private function checkFileUsageCount(Block $block, int $expectedUsageCount): void
    {
        $actualUsageCount = (int) app(Connection::class)->fetchOne(
            'SELECT COUNT(*) FROM FileUsageRecord WHERE block_id = :bID',
            ['bID' => $block->getBlockID()]
        );
        $this->assertSame($expectedUsageCount, $actualUsageCount, "The block should use {$expectedUsageCount} instead of {$actualUsageCount} distinct file(s)");
    }

    private function checkRichText(int $blockID, string $query, array $info): void
    {
        $richText = (string) app(Connection::class)->fetchOne($query, ['bID' => $blockID]);

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

    private function assertSameXML(string $expected, string $actual, bool $keepXmlElementsOrder): void
    {
        $expected = $this->normalizeXML($expected, false);
        $actual = $this->normalizeXML($actual, $keepXmlElementsOrder);

        $this->assertSame($expected, $actual);
    }

    private function normalizeXML(string $xml, bool $keepXmlElementsOrder): string
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
            $this->sortXMLChildElements($doc->documentElement);
        }

        return $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);
    }

    private function sortXMLChildElements(DOMElement $parentElement): void
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
            $this->sortXMLChildElements($childElement);
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
        if (TreeType::getByHandle('group') === null) {
            TreeType::add('group');
        }
        if (TreeType::getByHandle('topic') === null) {
            TreeType::add('topic');
        }
        if (TreeNodeType::getByHandle('category') === null) {
            TreeNodeType::add('category');
        }
        if (TreeNodeType::getByHandle('group') === null) {
            TreeNodeType::add('group');
        }
        if (TreeNodeType::getByHandle('topic') === null) {
            TreeNodeType::add('topic');
        }
    }

    private static function createPages(): void
    {
        self::$blockPage = static::createPage('Page 1');
        static::createPage('Page 2');
        static::createPage('Page 3');
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
        self::$topicsTree = TopicService::add('Test Topic Tree');
        $root = self::$topicsTree->getRootTreeNodeObject();
        $child = TopicTreeNode::add('Parent #1', $root);
        TopicTreeNode::add('Child #1.1', $child);
        $child = TopicTreeNode::add('Parent #2', $root);
        TopicTreeNode::add('Child #2.1', $child);
        TopicTreeNode::add('Child #2.2', $child);
        TopicTreeNode::add('Child #2.3', $child);
    }

    private static function createAttributes(): void
    {
        $typeFactory = app(AttributeTypeFactory::class);
        $categoryService = app(AttributeCategoryService::class);
        if (($pageCategoryEntity = $categoryService->getByHandle('collection')) === null) {
            $pageCategory = $categoryService->add('collection');
        } else {
            $pageCategory = $pageCategoryEntity->getController();
        }
        /** @var \Concrete\Core\Attribute\Category\PageCategory $pageCategory */
        if (($topicsType = $typeFactory->getByHandle('topics')) === null) {
            $topicsType = $typeFactory->add('topics', 'Topics');
        }
        $categoryTypes = $pageCategory->getAttributeTypes();
        if (!$categoryTypes->contains($topicsType)) {
            $categoryTypes->add($topicsType);
        }
        $topicsController = $topicsType->getController();
        /** @var \Concrete\Attribute\Topics\Controller $topicsController */
        $settings = $topicsController->createAttributeKeySettings();
        /** @var \Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings $settings */
        $settings->setTopicTreeID(self::$topicsTree->getTreeID());
        $pageCategory->add($topicsType, ['akHandle' => 'test_topic', 'akName' => 'Test Topic'], $settings);
        app(EntityManagerInterface::class)->flush();
    }

    private static function createThumbnailTypes(): void
    {
        $em = app(EntityManagerInterface::class);
        $type = new Entity\File\Image\Thumbnail\Type\Type();
        $type->setHandle('thumbtype');
        $type->setName('Thumb Type');
        $type->setWidth(10);
        $type->setHeight(10);
        $type->setSizingMode(Entity\File\Image\Thumbnail\Type\Type::RESIZE_EXACT);
        $em->persist($type);
        $em->flush();
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
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-1.jpg', 'file-1.jpg', $importOptions);
        $importOptions->setCustomPrefix('210987654321');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-2.png', 'file-2.png', $importOptions);
        $importOptions->setCustomPrefix('123456543210');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-2.png', 'file-3.png', $importOptions);
        FileSet::create('Test File Set #1');
        FileSet::create('Test File Set #2');
        FileSet::create('Test File Set #3');
        $rootFileFolder->add('Sample File Folder #1', $rootFileFolder);
        $folder = $rootFileFolder->add('Sample File Folder #2', $rootFileFolder);
        $rootFileFolder->add('Child Folder', $folder);
        $rootFileFolder->add('Sample File Folder #3', $rootFileFolder);
    }

    private static function createBoards(): void
    {
        $board = new Entity\Board\Board();
        $board->setBoardName('Blog');
        $em = app(EntityManagerInterface::class);
        $em->persist($board);
        $em->flush();
    }

    private static function createCalendars(): void
    {
        $calendar = new Entity\Calendar\Calendar();
        $calendar->setName('Calendar Name');
        $em = app(EntityManagerInterface::class);
        $em->persist($calendar);
        $em->flush();
    }

    private static function createContainers(): void
    {
        $container = new Entity\Page\Container();
        $container->setContainerIcon('full.png');
        $container->setContainerHandle('container_1');
        $container->setContainerName('Container One');
        $em = app(EntityManagerInterface::class);
        $em->persist($container);
        $em->flush();
    }

    private static function createStacks(): void
    {
        PageType::add([
            'handle' => STACKS_PAGE_TYPE,
            'name' => 'Stack',
            'internal' => 1,
        ]);
        SinglePage::addGlobal(STACKS_PAGE_PATH);
        $stackFolderService = app(StackFolderService::class);
        Stack::addStack('Stack 1 in root folder');
        Stack::addStack('Stack 2 in root folder');
        $stackFolder1 = $stackFolderService->add('Stack Folder 1');
        $stackFolder11 = $stackFolderService->add('Sub folder 1 of Stack Folder 1', $stackFolder1);
        Stack::addStack('Stack 3 in sub folder', $stackFolder11);
        Stack::addStack('Stack 4 in sub folder', $stackFolder11);
    }

    private static function createSocialLinks(): void
    {
        $em = app(EntityManagerInterface::class);
        $repo = $em->getRepository(SocialLink::class);
        $site = self::$blockPage->getSite();
        if ($repo->findOneBy(['site' => $site, 'ssHandle' => 'bluesky']) === null) {
            $link = new SocialLink();
            $link->setServiceHandle('bluesky');
            $link->setSite($site);
            $link->setURL('https://bsky.app/profile/concretecms.bsky.social');
            $em->persist($link);
        }
        if ($repo->findOneBy(['site' => $site, 'ssHandle' => 'github']) === null) {
            $link = new SocialLink();
            $link->setServiceHandle('github');
            $link->setSite($site);
            $link->setURL('https://github.com/concretecms');
            $em->persist($link);
        }
        $em->flush();
    }
}

<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Stack\Folder\FolderService as StackFolderService;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as ComposerControlType;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\TestHelpers\Page\PageTestCase;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use DOMXPath;
use Illuminate\Filesystem\Filesystem;
use SimpleXMLElement;

class ImportExportTest extends PageTestCase
{
    /**
     * @var \Concrete\Core\File\Service\VolatileDirectory
     */
    private static $storageVolatileDirectory;

    /**
     * @var \Concrete\Core\Page\Page
     */
    private static $blockPage;

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
            'PageTypeComposerControlTypes',
            'PageTypeComposerFormLayoutSetControls',
            'PageTypeComposerOutputControls',
            'PageTypePageTemplateDefaultPages',
            'Stacks',
            'TreeTypes',
            'Trees',
            'TreeFileFolderNodes',
            'TreeNodeTypes',
            'TreeNodes',
            'TreeNodePermissionAssignments',
            'TreeFileNodes',
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
            Entity\Attribute\Key\FileKey::class,
            Entity\Attribute\Value\FileValue::class,
            Entity\Board\Board::class,
            Entity\Board\InstanceLog::class,
            Entity\Block\BlockType\BlockType::class,
            Entity\Calendar\Calendar::class,
            Entity\File\File::class,
            Entity\File\Image\Thumbnail\Type\Type::class,
            Entity\File\StorageLocation\StorageLocation::class,
            Entity\File\StorageLocation\Type\Type::class,
            Entity\File\Version::class,
            Entity\Page\Container::class,
            Entity\Page\Container\Instance::class,
            Entity\Page\Container\InstanceArea::class,
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
        self::createPages();
        self::createUsers();
        self::createFiles();
        self::createBoards();
        self::createCalendars();
        self::createContainers();
        self::createStacks();
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

    public function provideCases(): array
    {
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
                    $options = json_decode($json, true, JSON_THROW_ON_ERROR);
                }
                $cases[] = [
                    $blockTypeHandle,
                    str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname()),
                    $options,
                ];
            }
        }

        return $cases;
    }

    /**
     * @dataProvider provideCases
     */
    public function testCIFImportExport(string $blockTypeHandle, string $cifFile, array $options): void
    {
        foreach (($options['requiredBlockTypes'] ?? []) as $requiredBlockTypeHandle) {
            if (BlockType::getByHandle($requiredBlockTypeHandle)) {
                continue;
            }
            $requiredBlockType = BlockType::installBlockType($requiredBlockTypeHandle);
            $this->assertInstanceOf(BlockTypeEntity::class, $requiredBlockType);
        }
        $blockType = BlockType::getByHandle($blockTypeHandle) ?: BlockType::installBlockType($blockTypeHandle);
        $this->assertInstanceOf(BlockTypeEntity::class, $blockType);
        $inputCif = simplexml_load_file($cifFile);
        $this->assertInstanceOf(SimpleXMLElement::class, $inputCif);
        $importerExporterMethod = $options['importerExporterMethod'] ?? 'importExportBlockType';
        $outputCif = $this->{$importerExporterMethod}($blockType, $inputCif, $options);
        $this->assertSameXML($inputCif->asXML(), $outputCif);
    }

    private function importExportBlockType(BlockTypeEntity $blockType, SimpleXMLElement $inputCif, array $options, &$createdBlock = null): string
    {
        $blockType->loadController();
        $blockController = $blockType->getController();
        $this->assertInstanceOf(BlockController::class, $blockController);
        $createdBlock = $blockController->import(self::$blockPage, 'Main', $inputCif);
        $this->assertInstanceOf(Block::class, $createdBlock);
        $this->checkFileUsageCount($createdBlock, $options['fileUsageCount'] ?? 0);
        if (isset($options['richTextsWithPages'])) {
            $this->checkFieldRegexes($createdBlock, $options['richTextsWithPages'], '/\{CCM:CID_\d+\}/');
        }
        if (isset($options['richTextsWithImages'])) {
            $this->checkFieldRegexes($createdBlock, $options['richTextsWithImages'], '/<concrete-picture\b[^>]*\b(fid|fID)\s*=\s*["\']?[1-9]\d*\b"/s');
        }
        if (isset($options['richTextsWithFiles'])) {
            $this->checkFieldRegexes($createdBlock, $options['richTextsWithFiles'], '/\{CCM:FID_DL_[0-9a-fA-F][0-9a-fA-F\-]+[0-9a-fA-F]\}/');
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

    public function testBlockTypeCoverage(): void
    {
        $fs = new Filesystem();
        $availableHandles = array_map('basename', $fs->directories(DIR_FILES_BLOCK_TYPES_CORE));
        $coveredHandles = array_unique(
            array_map(
                static function (array $case): string {
                    return $case[0];
                },
                $this->provideCases()
            )
        );
        $expectedUncoveredHandles = [
            'core_board_slot', // Does it make sense to test it?
            'desktop_concrete_latest',
            'desktop_draft_list',
            'desktop_featured_addon',
            'desktop_featured_theme',
            'desktop_latest_form',
            'desktop_latest_health_result',
            'desktop_site_activity',
            'desktop_waiting_for_me',
            'document_library',
            'event_list',
            'express_entry_detail',
            'express_entry_list',
            'express_form',
            'external_form',
            'faq',
            'feature',
            'feature_link',
            'file',
            'form',
            'gallery',
            'google_map',
            'hero_image',
            'horizontal_rule',
            'html',
            'image',
            'image_slider',
            'next_previous',
            'page_attribute_display',
            'page_list',
            'page_title',
            'rss_displayer',
            'search',
            'share_this_page',
            'social_links',
            'survey',
            'switch_language',
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

    protected function checkFileUsageCount(Block $block, int $expectedUsageCount): void
    {
        $actualUsageCount = (int) app(Connection::class)->fetchOne(
            'SELECT COUNT(*) FROM FileUsageRecord WHERE block_id = :bID',
            ['bID' => $block->getBlockID()]
        );
        $this->assertSame($expectedUsageCount, $actualUsageCount, "The block should use {$expectedUsageCount} instead of {$actualUsageCount} distinct file(s)");
    }

    private function checkFieldRegexes(Block $block, array $queriesAndMatchCounts, string $regex): void
    {
        foreach ($queriesAndMatchCounts as $query => $matchCount) {
            $this->checkFieldRegex($block, $query, $matchCount, $regex);
        }
    }

    private function checkFieldRegex(Block $block, string $query, int $expectedMatchCount, string $regex): void
    {
        $richText = app(Connection::class)->fetchOne($query, ['bID' => $block->getBlockID()]);
        $this->assertNotSame(false, $richText);
        $this->assertNotNull($richText);
        $actualMatchCount = preg_match_all($regex, $richText);
        $this->assertSame($expectedMatchCount, $actualMatchCount, "The rich text\n{$richText}\nshould match {$regex} {$expectedMatchCount} time(s) instead of {$actualMatchCount}");
    }

    private function assertSameXML(string $expected, string $actual): void
    {
        $expected = $this->normalizeXML($expected);
        $actual = $this->normalizeXML($actual);

        $this->assertSame($expected, $actual);
    }

    private function normalizeXML(string $xml): string
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

        return $doc->saveXML();
    }

    private static function createPages(): void
    {
        self::$blockPage = static::createPage('Page 1');
        static::createPage('Page 2');
        static::createPage('Page 3');
    }

    private static function createUsers(): void
    {
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
        app(\Concrete\Core\File\Filesystem::class)->create();
        $importer = app(FileImporter::class);
        $importOptions = app(ImportOptions::class)
            ->setCanChangeLocalFile(false)
        ;
        $importOptions->setCustomPrefix('123456789012');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-1.jpg', 'file-1.jpg', $importOptions);
        $importOptions->setCustomPrefix('210987654321');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-2.png', 'file-2.png', $importOptions);
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
}

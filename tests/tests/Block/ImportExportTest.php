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
use Concrete\Core\File\Set\Set as FileSet;
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
use DOMElement;
use DOMNode;
use DOMXPath;
use Mockery as M;
use Illuminate\Filesystem\Filesystem;
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
                    $options = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
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
        if (isset($options['skipReason'])) {
            $this->markTestSkipped($options['skipReason']);
        }
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
        $outputCif = $this->{$importerExporterMethod}($blockType, $inputCif, $options);
        $this->assertSameXML($inputCif->asXML(), $outputCif, $options['keepXmlElementsOrder'] ?? false);
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
            'core_board_slot', // does it make sense to test it?
            'form', // old stuff that's not worth working on
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
}

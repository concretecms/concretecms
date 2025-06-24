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
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Page\PageTestCase;
use DOMDocument;
use DOMXPath;
use Illuminate\Filesystem\Filesystem;
use SimpleXMLElement;

class ImportExportTest extends PageTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'Blocks',
            'BlockTypeSets',
            'TreeTypes',
            'Trees',
            'TreeFileFolderNodes',
            'TreeNodeTypes',
            'TreeNodes',
            'TreeNodePermissionAssignments',
            'TreeFileNodes',
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
            Entity\Block\BlockType\BlockType::class,
            Entity\File\File::class,
            Entity\File\Image\Thumbnail\Type\Type::class,
            Entity\File\StorageLocation\StorageLocation::class,
            Entity\File\StorageLocation\Type\Type::class,
            Entity\File\Version::class,
            Entity\Statistics\UsageTracker\FileUsageRecord::class,
            Entity\StyleCustomizer\Inline\StyleSet::class,
        ]);
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
        $storageDirectory = $this->app->make(VolatileDirectory::class, ['parentDirectory' => sys_get_temp_dir()]);
        $this->initializeFilesystem($storageDirectory->getPath());
        $this->importTestFiles();
        $blockPage = $this->createTestPages();
        $blockType = BlockType::installBlockType($blockTypeHandle);
        $this->assertInstanceOf(BlockTypeEntity::class, $blockType);
        $blockType->loadController();
        $blockController = $blockType->getController();
        $this->assertInstanceOf(BlockController::class, $blockController);
        $inputCif = simplexml_load_file($cifFile);
        $this->assertInstanceOf(SimpleXMLElement::class, $inputCif);
        $block = $blockController->import($blockPage, 'Main', $inputCif);
        $this->assertInstanceOf(Block::class, $block);
        $this->checkFileUsageCount($block, $options['fileUsageCount'] ?? 0);
        if (isset($options['richTextsWithPages'])) {
            $this->checkFieldRegexes($block, $options['richTextsWithPages'], '/\{CCM:CID_\d+\}/');
        }
        if (isset($options['richTextsWithImages'])) {
            $this->checkFieldRegexes($block, $options['richTextsWithImages'], '/<concrete-picture\b[^>]*\b(fid|fID)\s*=\s*["\']?[1-9]\d*\b"/s');
        }
        if (isset($options['richTextsWithFiles'])) {
            $this->checkFieldRegexes($block, $options['richTextsWithFiles'], '/\{CCM:FID_DL_[0-9a-fA-F][0-9a-fA-F\-]+[0-9a-fA-F]\}/');
        }
        $outputCif = simplexml_load_string('<root />');
        $block->export($outputCif);
        $this->assertTrue(isset($outputCif->block));
        $this->assertSameXML($inputCif->asXML(), $outputCif->block->asXML());
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
            'accordion',
            'autonav',
            'board',
            'breadcrumbs',
            'calendar',
            'calendar_event',
            'content',
            'core_area_layout',
            'core_board_slot',
            'core_container',
            'core_conversation',
            'core_page_type_composer_control_output',
            'core_scrapbook_display',
            'core_stack_display',
            'core_theme_documentation_breadcrumb',
            'core_theme_documentation_toc',
            'date_navigation',
            'desktop_app_status',
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

    protected function checkFieldRegexes(Block $block, array $queriesAndMatchCounts, string $regex): void
    {
        foreach ($queriesAndMatchCounts as $query => $matchCount) {
            $this->checkFieldRegex($block, $query, $matchCount, $regex);
        }
    }

    protected function checkFieldRegex(Block $block, string $query, int $expectedMatchCount, string $regex): void
    {
        $richText = app(Connection::class)->fetchOne($query, ['bID' => $block->getBlockID()]);
        $this->assertNotSame(false, $richText);
        $this->assertNotNull($richText);
        $actualMatchCount = preg_match_all($regex, $richText);
        $this->assertSame($expectedMatchCount, $actualMatchCount, "The rich text\n{$richText}\nshould match {$regex} {$expectedMatchCount} time(s) instead of {$actualMatchCount}");
    }

    protected function assertSameXML(string $expected, string $actual): void
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

    private function initializeFilesystem(string $path): void
    {
        $storageLocationType = StorageLocationType::add('local', 'Local Storage');
        $storageLocationConfiguration = $storageLocationType->getConfigurationObject();
        $storageLocationConfiguration->setRootPath($path);
        $storageLocationConfiguration->setWebRootRelativePath('/application/files');
        $storageLocationFactory = $this->app->make(StorageLocationFactory::class);
        $storageLocation = $storageLocationFactory->create($storageLocationConfiguration, 'Default');
        $storageLocation->setIsDefault(true);
        $storageLocationFactory->persist($storageLocation);
        $this->app->make(\Concrete\Core\File\Filesystem::class)->create();
    }

    private function importTestFiles(): void
    {
        $importer = $this->app->make(FileImporter::class);
        $importOptions = $this->app->make(ImportOptions::class)
            ->setCanChangeLocalFile(false)
        ;
        $importOptions->setCustomPrefix('123456789012');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-1.jpg', 'file-1.jpg', $importOptions);
        $importOptions->setCustomPrefix('210987654321');
        $importer->importLocalFile(DIR_TESTS . '/assets/Block/cif/file-2.png', 'file-2.png', $importOptions);
    }

    private function createTestPages(): Page
    {
        $blockPage = $this->createPage('Page 1');
        $this->createPage('Page 2');

        return $blockPage;
    }
}

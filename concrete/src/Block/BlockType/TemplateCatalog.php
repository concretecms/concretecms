<?php

declare(strict_types=1);

namespace Concrete\Core\Block\BlockType;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\File\Service\File;
use Concrete\Core\Filesystem\TemplateFile;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Theme\Theme;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @see \Concrete\Core\Block\View\BlockViewTemplate::computeView() which turns such a name into a path
 */
class TemplateCatalog
{
    /**
     * @var \Concrete\Core\File\Service\File
     */
    protected $fileHelper;

    /**
     * @var \Concrete\Core\Package\PackageService
     */
    protected $packageService;

    /**
     * The names found under a set of directories, by the key of the set: they are read once for as long as this object lives.
     *
     * @var array<string, string[]>
     */
    private $names = [];

    /**
     * Array keys: the package handles; array values: the package directories.
     *
     * @var array<string, string>|null
     */
    private $packageDirectories = null;

    public function __construct(File $fileHelper, PackageService $packageService)
    {
        $this->fileHelper = $fileHelper;
        $this->packageService = $packageService;
    }

    /**
     * Get the custom templates that can be used for a block type (from application, packages, core, and - if provided - from a theme).
     *
     * @return \Concrete\Core\Filesystem\TemplateFile[]
     */
    public function getCustomTemplates(BlockTypeEntity $blockType, ?Theme $theme): array
    {
        $names = $this->getNames($blockType->getBlockTypeHandle(), DIRNAME_BLOCK_TEMPLATES, $theme);

        return $this->wrap($blockType, $names);
    }

    /**
     * Get the custom templates that can be used for a block type (only the ones from a specific page theme).
     *
     * @return \Concrete\Core\Filesystem\TemplateFile[]
     */
    public function getThemeCustomTemplates(BlockTypeEntity $blockType, Theme $theme): array
    {
        $names = $this->getNames($blockType->getBlockTypeHandle(), DIRNAME_BLOCK_TEMPLATES, $theme, true);

        return $this->wrap($blockType, $names);
    }

    /**
     * Get the custom templates that can be used for a block (from application, packages, core, and the theme of the page containing the block).
     *
     * @return \Concrete\Core\Filesystem\TemplateFile[]
     */
    public function getCustomTemplatesForBlock(Block $block): array
    {
        $blockType = $block->getBlockTypeObject();
        if (!$blockType) {
            return [];
        }
        $collection = $block->getBlockCollectionObject();
        $theme = $collection ? $collection->getCollectionThemeObject() : null;

        return $this->getCustomTemplates($blockType, $theme instanceof Theme ? $theme : null);
    }

    /**
     * Get the composer templates that can be used for a block type (from application, packages, and core).
     *
     * @return \Concrete\Core\Filesystem\TemplateFile[]
     */
    public function getComposerTemplates(BlockTypeEntity $blockType): array
    {
        $names = $this->getNames($blockType->getBlockTypeHandle(), DIRNAME_BLOCK_TEMPLATES_COMPOSER, null);

        return $this->wrap($blockType, $names);
    }

    /**
     * Check if a name is one of the custom templates that can be used for a block type, as seen by a page shown with a theme.
     */
    public function isValidCustomTemplate(BlockTypeEntity $blockType, ?Theme $theme, string $filename): bool
    {
        return $this->isAmong($this->getCustomTemplates($blockType, $theme), $filename);
    }

    /**
     * Check if a name is one of the custom templates that can be used for a block.
     */
    public function isValidCustomTemplateForBlock(Block $block, string $filename): bool
    {
        return $this->isAmong($this->getCustomTemplatesForBlock($block), $filename);
    }

    /**
     * @param \Concrete\Core\Filesystem\TemplateFile[] $templates
     */
    private function isAmong(array $templates, string $filename): bool
    {
        if ($filename === '') {
            return false;
        }
        foreach ($templates as $template) {
            if ($template->getTemplateFileFilename() === $filename) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, string> where the installed packages live, by handle
     */
    protected function getPackageDirectories(): array
    {
        if ($this->packageDirectories === null) {
            $packageDirectories = [];
            foreach ($this->packageService->getInstalledList() as $package) {
                $handle = $package->getPackageHandle();
                $directory = DIR_PACKAGES . '/' . $handle;
                if (!is_dir($directory)) {
                    $directory = DIR_PACKAGES_CORE . '/' . $handle;
                }
                $packageDirectories[$handle] = $directory;
            }
            $this->packageDirectories = $packageDirectories;
        }

        return $this->packageDirectories;
    }

    /**
     * @param bool $themeOnly TRUE for the directories of the theme alone
     *
     * @return string[] the names of the files and of the directories found
     */
    private function getNames(string $blockTypeHandle, string $which, ?Theme $theme, bool $themeOnly = false): array
    {
        if ($themeOnly && $theme === null) {
            return [];
        }
        $key = implode('|', [
            $blockTypeHandle,
            $which,
            $theme === null ? '' : $theme->getThemeHandle(),
            $themeOnly ? 'theme' : '',
        ]);
        if (!isset($this->names[$key])) {
            $names = [];
            $directories = $themeOnly ? $this->getThemeDirectories($blockTypeHandle, $which, $theme) : $this->getDirectories($blockTypeHandle, $which, $theme);
            foreach ($directories as $directory) {
                $names = array_merge($names, $this->fileHelper->getDirectoryContents($directory));
            }
            $this->names[$key] = $names;
        }

        return $this->names[$key];
    }

    /**
     * @return string[]
     */
    private function getDirectories(string $blockTypeHandle, string $which, ?Theme $theme): array
    {
        $directories = [
            DIR_FILES_BLOCK_TYPES . '/' . $blockTypeHandle . '/' . $which,
        ];
        if ($theme !== null) {
            $directories = array_merge($directories, $this->getThemeDirectories($blockTypeHandle, $which, $theme));
        }
        foreach ($this->getPackageDirectories() as $package) {
            $directories[] = $package . '/' . DIRNAME_BLOCKS . '/' . $blockTypeHandle . '/' . $which;
        }
        $directories[] = DIR_FILES_BLOCK_TYPES_CORE . '/' . $blockTypeHandle . '/' . $which;

        return $directories;
    }

    /**
     * @return string[]
     */
    private function getThemeDirectories(string $blockTypeHandle, string $which, Theme $theme): array
    {
        $inside = '/' . $theme->getThemeHandle() . '/' . DIRNAME_BLOCKS . '/' . $blockTypeHandle . '/' . $which;
        $directories = [
            DIR_FILES_THEMES . $inside,
        ];
        $packageHandle = (string) $theme->getPackageHandle();
        if ($packageHandle !== '') {
            $packageDir = $this->getPackageDirectories()[$packageHandle] ?? '';
            if ($packageDir !== '') {
                $directories[] = $packageDir . '/' . DIRNAME_THEMES . $inside;
            }
        }
        $directories[] = DIR_FILES_THEMES_CORE . $inside;

        return $directories;
    }

    /**
     * @param string[] $names
     *
     * @return \Concrete\Core\Filesystem\TemplateFile[]
     */
    private function wrap(BlockTypeEntity $blockType, array $names): array
    {
        $templates = [];
        foreach (array_unique($names) as $name) {
            $templates[] = new TemplateFile($blockType, $name);
        }

        return TemplateFile::sortTemplateFileList($templates);
    }
}

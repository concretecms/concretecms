<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\Package\Packer\PackerFile;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Filter out files accordingly to their type.
 */
class FileExcluder implements FilterInterface
{
    /**
     * Files to exclude: none.
     *
     * @var int
     */
    const EXCLUDE_NONE = 0b0;

    /**
     * Files to exclude: all.
     *
     * @var int
     */
    const EXCLUDE_ALL = 0b1111111111111111111111111111111;

    /**
     * Files to exclude: files that start with a dot.
     *
     * @var int
     */
    const EXCLUDE_DOT = 0b1;

    /**
     * Files to exclude: translation dictionary files (.pot).
     *
     * @var int
     */
    const EXCLUDE_POT = 0b10;

    /**
     * Files to exclude: translation source files (.po).
     *
     * @var int
     */
    const EXCLUDE_PO = 0b100;

    /**
     * Files to exclude: icons in SVG format (.svg) for package, themes, block types.
     *
     * @var int
     */
    const EXCLUDE_SVGICON = 0b1000;

    /**
     * Files to exclude: composer.json files.
     *
     * @var int
     */
    const EXCLUDE_COMPOSER_JSON = 0b10000;

    /**
     * Files to exclude: composer.json and composer.lock files.
     *
     * @var int
     */
    const EXCLUDE_COMPOSER_LOCK = 0b110000; // includes EXCLUDE_COMPOSER_JSON

    /**
     * The EXCLUDE_... bit flags.
     *
     * @var int
     */
    protected $excludeFiles;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Initialize the instance.
     *
     * @param int $excludeFiles the EXCLUDE_... bit flags
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct($excludeFiles, OutputInterface $output)
    {
        $this->excludeFiles = $excludeFiles;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Filter\FilterInterface::apply()
     */
    public function apply(PackerFile $file)
    {
        $exclusionReason = $this->getExclusionReason($file);
        if ($exclusionReason === null) {
            return [$file];
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln(t(/*i18n: %1$s is a file/directory name, %2$s is the reason why it's excluded*/'Excluding %1$s: %2$s', $file->getRelativePath(), $exclusionReason));
        }

        return [];
    }

    /**
     * Should a file be excluded accordingly to the configured flags?
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file
     *
     * @return string|null a message describing why a file is excluded, or NULL if the file shouldn't be excluded
     */
    protected function getExclusionReason(PackerFile $file)
    {
        if ($this->excludeFiles & static::EXCLUDE_DOT) {
            $basename = $file->getBasename();
            if ($basename[0] === '.') {
                return t('The name starts with a dot');
            }
        }
        if ($file->isDirectory()) {
            return null;
        }
        switch ($file->getType()) {
            case PackerFile::TYPE_TRANSLATIONS_POT:
                if ($this->excludeFiles & static::EXCLUDE_POT) {
                    return t('The file is a source .pot translation file.');
                }
                break;
            case PackerFile::TYPE_TRANSLATIONS_PO:
                if ($this->excludeFiles & static::EXCLUDE_PO) {
                    return t('The file is a source .po translation file.');
                }
                break;
            case PackerFile::TYPE_SVGICON_BLOCKTYPE:
            case PackerFile::TYPE_SVGICON_PACKAGE:
            case PackerFile::TYPE_SVGICON_THEME:
                if ($this->excludeFiles & static::EXCLUDE_SVGICON) {
                    return t('The file is a source .svg icon file.');
                }
                break;
        }
        switch (strtolower($file->getBasename())) {
            case 'composer.json':
                if ($this->excludeFiles & static::EXCLUDE_COMPOSER_JSON) {
                    $this->output->writeln(t('The file is a composer.json file.'));
                }
                break;
            case 'composer.lock':
                if ($this->excludeFiles & static::EXCLUDE_COMPOSER_LOCK) {
                    $this->output->writeln(t('The file is a composer.lock file.'));
                }
                break;
        }

        return null;
    }
}

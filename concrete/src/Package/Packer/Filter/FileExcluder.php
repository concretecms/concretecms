<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\Package\Packer\PackerFile;
use Symfony\Component\Console\Output\OutputInterface;

class FileExcluder implements FilterInterface
{
    /**
     * Files to keep in archive: none.
     *
     * @var int
     */
    const KEEPFILES_NONE = 0b0;

    /**
     * Files to keep in archive: files that start with a dot.
     *
     * @var int
     */
    const KEEPFILES_DOT = 0b1;

    /**
     * Files to keep in the archive: translation dictionary files (.pot).
     *
     * @var int
     */
    const KEEPFILES_POT = 0b10;

    /**
     * Files to keep in the archive: translation source files (.po).
     *
     * @var int
     */
    const KEEPFILES_PO = 0b100;

    /**
     * Files to keep in the archive: icons in SVG format (.svg) for package, themes, block types.
     *
     * @var int
     */
    const KEEPFILES_SVGICON = 0b1000;

    /**
     * Files to keep in the archive: composer.json files.
     *
     * @var int
     */
    const KEEPFILES_COMPOSER_JSON = 0b10000;

    /**
     * Files to keep in the archive: composer.json and composer.lock files.
     *
     * @var int
     */
    const KEEPFILES_COMPOSER_LOCK = 0b110000; // includes KEEPFILES_COMPOSER_JSON

    /**
     * @var int
     */
    protected $keepFiles;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @param int $keepFiles
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct($keepFiles, OutputInterface $output)
    {
        $this->keepFiles = $keepFiles;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Filter\FilterInterface::apply()
     */
    public function apply(PackerFile $file)
    {
        $basename = $file->getBasename();
        if (($this->keepFiles & static::KEEPFILES_DOT) === 0 && $basename[0] === '.') {
            if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                $this->output->writeln(t('Skipping file starting with a dot: %s', $file->getRelativePath()));
            }

            return [];
        }
        if (!$file->isDirectory()) {
            switch ($file->getType()) {
                case PackerFile::TYPE_TRANSLATIONS_POT:
                    if (($this->keepFiles & static::KEEPFILES_POT) === 0) {
                        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                            $this->output->writeln(t('Skipping translation .pot file: %s', $file->getRelativePath()));
                        }

                        return [];
                    }
                    break;
                case PackerFile::TYPE_TRANSLATIONS_PO:
                    if (($this->keepFiles & static::KEEPFILES_PO) === 0) {
                        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                            $this->output->writeln(t('Skipping translation .po file: %s', $file->getRelativePath()));
                        }

                        return [];
                    }
                    break;
                case PackerFile::TYPE_SVGICON_BLOCKTYPE:
                case PackerFile::TYPE_SVGICON_PACKAGE:
                case PackerFile::TYPE_SVGICON_THEME:
                    if (($this->keepFiles & static::KEEPFILES_SVGICON) === 0) {
                        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                            $this->output->writeln(t('Skipping source SVG icon file: %s', $file->getRelativePath()));
                        }

                        return [];
                    }
                    break;
                default:
                    switch ($basename) {
                        case 'composer.json':
                            if (($this->keepFiles & static::KEEPFILES_COMPOSER_JSON) === 0) {
                                if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                                    $this->output->writeln(t('Skipping composer.json file: %s', $file->getRelativePath()));
                                }

                                return [];
                            }
                            break;
                        case 'composer.lock':
                            if (($this->keepFiles & static::KEEPFILES_COMPOSER_LOCK) === 0) {
                                if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_PLAIN) {
                                    $this->output->writeln(t('Skipping composer.lock file: %s', $file->getRelativePath()));
                                }

                                return [];
                            }
                            break;
                    }
            }
        }

        return [$file];
    }
}

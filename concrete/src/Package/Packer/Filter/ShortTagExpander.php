<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Packer\PackerFile;
use Concrete\Core\Support\ShortTagExpander as Expander;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Expand the short PHP open tags (and optionally the short PHP echo tags).
 */
class ShortTagExpander implements FilterInterface
{
    /**
     * Should the short PHP echo tags be expanded too?
     *
     * @var bool
     */
    protected $expandEcho;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Concrete\Core\File\Service\VolatileDirectory
     */
    protected $volatileDirectory;

    /**
     * @var \Concrete\Core\Support\ShortTagExpander
     */
    protected $expander;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Initialize the instance.
     *
     * @param bool $expandEcho set to tru to expand also short PHP echo tags
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Concrete\Core\File\Service\VolatileDirectory $volatileDirectory
     * @param \Concrete\Core\Support\ShortTagExpander $expander
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct($expandEcho, OutputInterface $output, VolatileDirectory $volatileDirectory, Expander $expander, Filesystem $fs)
    {
        $this->expandEcho = (bool) $expandEcho;
        $this->output = $output;
        $this->volatileDirectory = $volatileDirectory;
        $this->expander = $expander;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Packer\Filter\FilterInterface::apply()
     */
    public function apply(PackerFile $file)
    {
        if ($file->isDirectory() || $file->getExtension() !== 'php') {
            return [$file];
        }
        $originalContents = $this->fs->get($file->getAbsolutePath());
        $expandedContents = $this->expander->expandCode($originalContents, $this->expandEcho);
        if ($expandedContents === null) {
            return [$file];
        }
        if ($this->output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $this->output->writeln(t('Short PHP tags expanded in file %s', $file->getRelativePath()));
        }
        $newFile = tempnam($this->volatileDirectory->getPath(), 'xtag');
        $this->fs->put($newFile, $expandedContents);

        return [PackerFile::newChangedFile($file, $newFile)];
    }
}

<?php

namespace Concrete\Core\StyleCustomizer\Writer;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Illuminate\Filesystem\Filesystem;
use tubalmartin\CssMin\Minifier;

/**
 * Responsible for writing CSS into the filesystem for use with a particular skin.
 */
class Writer
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Minifier
     */
    protected $minifier;

    /**
     * Writer constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, Minifier $minifier)
    {
        $this->filesystem = $filesystem;
        $this->minifier = $minifier;
    }

    public function writeStyles(CustomSkin $skin, string $styles)
    {
        $styles = $this->minifier->run($styles);
        $directory = DIR_FILES_UPLOADED_STANDARD . DIRECTORY_SEPARATOR . DIRNAME_STYLE_CUSTOMIZER_PRESETS;
        if (!$this->filesystem->isDirectory($directory)) {
            $this->filesystem->makeDirectory($directory);
        }
        $file = $directory . DIRECTORY_SEPARATOR . $skin->getIdentifier() . '.css';
        $this->filesystem->delete($file);
        $this->filesystem->put($file, $styles);
    }

    public function clearStyles(CustomSkin $skin)
    {
        $directory = DIR_FILES_UPLOADED_STANDARD . DIRECTORY_SEPARATOR . DIRNAME_STYLE_CUSTOMIZER_PRESETS;
        $file = $directory . DIRECTORY_SEPARATOR . $skin->getIdentifier() . '.css';
        if ($this->filesystem->isFile($file)) {
            $this->filesystem->delete($file);
        }
    }

}

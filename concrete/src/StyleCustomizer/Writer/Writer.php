<?php

namespace Concrete\Core\StyleCustomizer\Writer;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Illuminate\Filesystem\Filesystem;

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
     * Writer constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function writeStyles(CustomSkin $skin, string $styles)
    {
        $directory = DIR_FILES_UPLOADED_STANDARD . DIRECTORY_SEPARATOR . DIRNAME_STYLE_CUSTOMIZER_SKINS;
        if (!$this->filesystem->isDirectory($directory)) {
            $this->filesystem->makeDirectory($directory);
        }
        $file = $directory . DIRECTORY_SEPARATOR . $skin->getIdentifier() . '.css';
        $this->filesystem->delete($file);
        $this->filesystem->put($file, $styles);
    }

}

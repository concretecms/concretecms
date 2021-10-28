<?php

namespace Concrete\Core\StyleCustomizer\Skin;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Utility\Service\Text;
use Illuminate\Filesystem\Filesystem;

class SkinFactory
{
    /**
     * @var Text
     */
    protected $textService;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, Text $textService)
    {
        $this->filesystem = $filesystem;
        $this->textService = $textService;
    }

    public function createFromPath(string $path, Theme $theme): ?SkinInterface
    {
        $fileName = basename($path);
        $identifier = substr($fileName, 0, strpos($fileName, '.css'));
        $skin = new PresetSkin($identifier, $this->textService->unhandle($identifier), $theme);
        return $skin;
    }

    /**
     * Returns an array of SkinInterface objects found in the path.
     *
     * @param string $path
     */
    public function createMultipleFromDirectory(string $path, Theme $theme): array
    {
        $skins = [];
        foreach($this->filesystem->directories($path) as $skin) {
            $skin = $this->createFromPath($skin, $theme);
            if ($skin) {
                $skins[] = $skin;
            }
        }
        foreach($this->filesystem->files($path) as $skin) {
            $skin = $this->createFromPath($skin, $theme);
            if ($skin) {
                $skins[] = $skin;
            }
        }
        return $skins;
    }

}

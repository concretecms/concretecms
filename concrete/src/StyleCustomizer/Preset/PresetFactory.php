<?php

namespace Concrete\Core\StyleCustomizer\Preset;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Illuminate\Filesystem\Filesystem;

class PresetFactory
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    public function __construct(FileLocator $fileLocator, Filesystem $filesystem)
    {
        $this->fileLocator = $fileLocator;
        $this->filesystem = $filesystem;
    }

    /**
     * Returns an array of SkinInterface objects found in the path.
     *
     * @param string $path
     */
    public function createFromTheme(Theme $theme, TypeInterface $type): array
    {
        if ($theme->getPackageHandle()) {
            $this->fileLocator->addPackageLocation($theme->getPackageHandle());
        }

        $r = $this->fileLocator->getRecord(
            DIRNAME_THEMES .
            DIRECTORY_SEPARATOR .
            $theme->getThemeHandle() .
            DIRECTORY_SEPARATOR .
            DIRNAME_CSS .
            DIRECTORY_SEPARATOR .
            DIRNAME_STYLE_CUSTOMIZER_PRESETS
        );

        $presets = [];
        $entries = $this->filesystem->directories($r->file);
        $entries = array_merge($entries, $this->filesystem->files($r->file));

        foreach ($entries as $path) {
            $preset = $type->createPresetFromPath($path, $theme);
            if ($preset) {
                $presets[] = $preset;
            }
        }
        usort($presets, function (PresetInterface $a, PresetInterface $b) {
            if ($a->getIdentifier() === 'default') {
                return -1;
            }
            if ($b->getIdentifier() === 'default') {
                return 1;
            }
            return strcmp($a->getName(), $b->getName()); // Sort by name
        });
        return $presets;
    }

}

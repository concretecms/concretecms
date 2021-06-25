<?php

namespace Concrete\Core\StyleCustomizer\Parser;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class ParserFactory
{

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * When given a particular theme, returns the parser that ought to be used with it.
     *
     * @param Theme $theme
     */
    public function createParserFromTheme(Theme $theme)
    {
        $record = $this->fileLocator->getRecord(
            DIRNAME_THEMES .'/' .
            $theme->getThemeHandle() . '/' .
            DIRNAME_SCSS,
            $theme->getPackageHandle(),
        );
        if ($record->exists()) {
            return new ScssParser();
        }

        return new LessParser();
    }

    /**
     * When given a particular theme's skin, returns the parser that ought to be used with it.
     *
     * Note: Currently on SCSS is supported for skins, so this only returns SCSS.
     *
     * @param SkinInterface $skin
     */
    public function createParserFromSkin(SkinInterface $skin)
    {
        return new ScssParser();
    }


}

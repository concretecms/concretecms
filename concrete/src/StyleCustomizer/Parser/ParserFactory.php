<?php

namespace Concrete\Core\StyleCustomizer\Parser;

use Concrete\Core\Application\Application;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class ParserFactory
{

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, FileLocator $fileLocator)
    {
        $this->app = $app;
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
            return $this->app->make(BedrockParser::class);
        }

        return $this->app->make(LegacyLessParser::class);
    }

    /**
     * When given a particular theme's skin, returns the parser that ought to be used with it.
     *
     * Note: Currently only SCSS is supported for skins, so this only returns SCSS.
     *
     * @param SkinInterface $skin
     */
    public function createParserFromSkin(SkinInterface $skin)
    {
        return $this->app->make(BedrockParser::class);
    }


}

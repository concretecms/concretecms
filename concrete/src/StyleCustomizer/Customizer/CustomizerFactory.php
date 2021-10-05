<?php
namespace Concrete\Core\StyleCustomizer\Customizer;

use Concrete\Core\Application\Application;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Theme\CustomizableInterface;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Customizer\Type\LegacyCustomizerType;
use Concrete\Core\StyleCustomizer\Customizer\Type\SkinCustomizerType;

class CustomizerFactory
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

    public function createFromTheme(Theme $theme)
    {
        $customizer = null;
        if ($theme->getPackageHandle()) {
            $this->fileLocator->addLocation(new FileLocator\PackageLocation($theme->getPackageHandle()));
        }
        $r = $this->fileLocator->getRecord(
            DIRNAME_THEMES .
            DIRECTORY_SEPARATOR .
            $theme->getThemeHandle() .
            DIRECTORY_SEPARATOR .
            DIRNAME_CSS .
            DIRECTORY_SEPARATOR .
            FILENAME_STYLE_CUSTOMIZER_STYLES
        );
        if ($r->exists()) {

            $customizer = $this->app->make(Customizer::class);
            $customizer->setConfigurationFile($r->file);
            $customizer->setTheme($theme);

            if ($theme instanceof CustomizableInterface) {
                $type = $theme->getThemeCustomizerType();
            } else {
                // Load the xml and see what happens
                $x = simplexml_load_file($r->file);
                if ($x) {
                    $version = (string)$x['version'];
                    if (version_compare($version, '2.0', '>=')) {
                        $language = (string) $x['lang'];
                        $type = $this->app->make(SkinCustomizerType::class);
                        $type->setLanguage($language);
                    } else {
                        $type = $this->app->make(LegacyCustomizerType::class);
                    }
                }
            }

            $customizer->setType($type);
        }
        return $customizer;
    }
}

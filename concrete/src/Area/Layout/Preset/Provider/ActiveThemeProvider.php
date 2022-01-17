<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Http\Request;
use Concrete\Core\Site\Service as SiteService;

class ActiveThemeProvider implements ProviderInterface
{
    /**
     * The name of this provider.
     *
     * @var string
     */
    protected $name;

    /**
     * The available layout presets.
     *
     * @var \Concrete\Core\Area\Layout\Preset\PresetInterface[]
     */
    protected $presets;

    /**
     * The theme that provides the presets.
     *
     * @var \Concrete\Core\Page\Theme\Theme|null
     */
    protected $theme;

    public function __construct(Request $request, SiteService $siteService)
    {
        $theme = null;
        $c = $request->getCurrentPage();
        if ($c && !$c->isError()) {
            $theme = $c->getCollectionThemeObject();
        }
        if ($theme === null) {
            $site = $siteService->getActiveSiteForEditing();
            if ($site !== null) {
                $themeID = $site->getThemeID();
                $theme = $themeID ? Theme::getByID($themeID) : null;
            }
        }
        if ($theme instanceof Theme) {
            $this->theme = $theme;
            $this->name = $theme->getThemeDisplayName();
            if ($theme instanceof ThemeProviderInterface) {
                $provider = new ThemeProvider($theme);
                $this->presets = $provider->getPresets();
            } else {
                $this->presets = [];
            }
        } else {
            $this->theme = null;
            $this->name = t('Active Theme');
            $this->presets = [];
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface::getPresets()
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Get the theme that provides the presets.
     */
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    /**
     * Get the handle of the theme that provides the presets.
     */
    public function getThemeHandle(): string
    {
        $theme = $this->getTheme();

        return $theme === null ? '' : (string) $theme->getThemeHandle();
    }
}

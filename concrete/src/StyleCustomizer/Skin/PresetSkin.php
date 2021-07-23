<?php

namespace Concrete\Core\StyleCustomizer\Skin;

use Concrete\Core\Page\Theme\Theme;
use HtmlObject\Element;

class PresetSkin implements SkinInterface
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * PresetSkin constructor.
     * @param string $directory
     * @param string $name
     */
    public function __construct(string $directory, string $identifier, string $name, Theme $theme)
    {
        $this->directory = $directory;
        $this->name = $name;
        $this->identifier = $identifier;
        $this->theme = $theme;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function jsonSerialize()
    {
        return [
            'identifier' => $this->getIdentifier(),
            'name' => $this->getName(),
        ];
    }

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    public function getStylesheet(): Element
    {
        $theme = $this->getTheme();
        $path = $theme->getSkinDirectoryRecord()->getUrl();
        $stylesheet = $path . '/' . $this->getIdentifier() . '/' . FILENAME_THEMES_SKIN_STYLESHEET_ENTRYPOINT;
        $element = new Element('link', null);
        $element->rel('stylesheet')->type('text/css')->href($stylesheet);
        return $element;
    }



}

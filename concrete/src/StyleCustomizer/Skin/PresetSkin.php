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
    protected $name;

    /**
     * @var Theme
     */
    protected $theme;

    public function __construct(string $identifier, string $name, Theme $theme)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->theme = $theme;
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

    #[\ReturnTypeWillChange]
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
        $stylesheet = $path . '/' . $this->getIdentifier() . '.css';
        $element = new Element('link', null);
        $element->setIsSelfClosing(true);
        $element->rel('stylesheet')->type('text/css')->href($stylesheet);
        return $element;
    }



}

<?php

namespace Concrete\Core\StyleCustomizer\Skin;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\Stylesheet\StylesheetInterface;
use HtmlObject\Element;

interface SkinInterface extends \JsonSerializable
{

    const SKIN_DEFAULT = 'default';

    public function getName(): string;

    public function getIdentifier(): string;

    public function getTheme(): Theme;

    public function getStylesheet(): StylesheetInterface;

}

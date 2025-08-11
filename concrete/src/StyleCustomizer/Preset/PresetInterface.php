<?php

namespace Concrete\Core\StyleCustomizer\Preset;

use Concrete\Core\Page\Theme\Theme;

interface PresetInterface extends \JsonSerializable
{

    public function getName(): string;

    public function getIdentifier(): string;

    public function getTheme(): Theme;

}

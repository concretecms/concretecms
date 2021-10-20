<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Preview\PreviewHandlerInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\ManagerInterface as ParserManagerInterface;
use Concrete\Core\StyleCustomizer\Customizations\ManagerInterface as CustomizationsManagerInterface;
interface TypeInterface
{

    public function getPresetType(): PresetTypeInterface;

    public function getVariableNormalizer(): NormalizerInterface;

    public function supportsCustomSkins(): bool;

    public function supportsPageCustomization(): bool;

    public function getStyleProcessor(): ProcessorInterface;

    public function getParserManager(): ParserManagerInterface;

    public function getPreviewHandler(): PreviewHandlerInterface;

    public function getCustomizationsManager(): CustomizationsManagerInterface;

}

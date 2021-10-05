<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;

interface TypeInterface
{

    public function getPresetType(): PresetTypeInterface;

    public function getVariableNormalizer(): NormalizerInterface;

    public function supportsCustomSkins(): bool;

    public function getStyleProcessor(): ProcessorInterface;

}

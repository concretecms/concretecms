<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Normalizer\LegacyNormalizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Preset\Type\LessFilePresetType;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Processor\LessProcessor;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;

class LegacyCustomizerType extends AbstractCustomizerType
{

    public function getPresetType(): PresetTypeInterface
    {
        return $this->app->make(LessFilePresetType::class);
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        return $this->app->make(LegacyNormalizer::class);
    }

    public function getStyleProcessor(): ProcessorInterface
    {
        return $this->app->make(LessProcessor::class);
    }

    public function supportsCustomSkins(): bool
    {
        return false;
    }

}

<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Normalizer\LessNormalizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Preset\Type\LessFilePresetType;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Processor\ScssProcessor;

class LegacyCustomizerType extends AbstractCustomizerType
{

    public function getPresetType(): PresetTypeInterface
    {
        return $this->app->make(LessFilePresetType::class);
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        return $this->app->make(LessNormalizer::class);
    }

    public function getStyleProcessor(): ProcessorInterface
    {
        return $this->app->make(ScssProcessor::class);
    }

    public function supportsCustomSkins(): bool
    {
        return false;
    }

}

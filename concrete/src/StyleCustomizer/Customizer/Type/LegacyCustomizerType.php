<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Customizations\LegacyCustomizationsManager;
use Concrete\Core\StyleCustomizer\Customizations\ManagerInterface as CustomizationsManagerInterface;
use Concrete\Core\StyleCustomizer\Normalizer\LegacyNormalizer;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Preset\Type\LessFilePresetType;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Preview\LegacyStylesheetPreviewHandler;
use Concrete\Core\StyleCustomizer\Preview\PreviewHandlerInterface;
use Concrete\Core\StyleCustomizer\Processor\LessProcessor;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\ManagerInterface;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\Version1Manager;

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

    public function getParserManager(): ManagerInterface
    {
        return $this->app->make(Version1Manager::class);
    }

    public function supportsCustomSkins(): bool
    {
        return false;
    }

    public function supportsPageCustomization(): bool
    {
        return true;
    }

    public function getPreviewHandler(): PreviewHandlerInterface
    {
        return $this->app->make(LegacyStylesheetPreviewHandler::class);
    }

    public function getCustomizationsManager(): CustomizationsManagerInterface
    {
        return $this->app->make(LegacyCustomizationsManager::class);
    }

}

<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\StyleCustomizer\Customizations\ManagerInterface as CustomizationsManagerInterface;
use Concrete\Core\StyleCustomizer\Customizations\SkinCustomizationsManager;
use Concrete\Core\StyleCustomizer\Customizer\Type\TypeInterface as CustomizerTypeInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Normalizer\ScssNormalizer;
use Concrete\Core\StyleCustomizer\Preset\Type\ScssDirectoryPresetType;
use Concrete\Core\StyleCustomizer\Preset\Type\TypeInterface as PresetTypeInterface;
use Concrete\Core\StyleCustomizer\Preview\PreviewHandlerInterface;
use Concrete\Core\StyleCustomizer\Preview\StandardPreviewHandler;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Processor\ScssProcessor;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\ManagerInterface;
use Concrete\Core\StyleCustomizer\Style\Parser\Manager\Version2Manager;

class SkinCustomizerType extends AbstractCustomizerType
{

    /**
     * scss or less
     *
     * @var string
     */
    protected $language;

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getPresetType(): PresetTypeInterface
    {
        return $this->app->make(ScssDirectoryPresetType::class);
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        return $this->app->make(ScssNormalizer::class);
    }

    public function getStyleProcessor(): ProcessorInterface
    {
        return $this->app->make(ScssProcessor::class);
    }

    public function getParserManager(): ManagerInterface
    {
        return $this->app->make(Version2Manager::class);
    }

    public function supportsCustomSkins(): bool
    {
        return true;
    }

    public function supportsPageCustomization(): bool
    {
        return false;
    }

    public function getPreviewHandler(): PreviewHandlerInterface
    {
        return $this->app->make(StandardPreviewHandler::class);
    }

    public function getCustomizationsManager(): CustomizationsManagerInterface
    {
        return $this->app->make(SkinCustomizationsManager::class);
    }
}

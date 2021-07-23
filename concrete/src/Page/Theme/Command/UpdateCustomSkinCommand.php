<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;

class UpdateCustomSkinCommand implements CommandInterface
{

    /**
     * @var CustomSkin
     */
    protected $customSkin;

    /**
     * @var NormalizedVariableCollection
     */
    protected $variableCollection;

    /**
     * @var string
     */
    protected $customCss;

    /**
     * @return CustomSkin
     */
    public function getCustomSkin(): CustomSkin
    {
        return $this->customSkin;
    }

    /**
     * @param CustomSkin $customSkin
     */
    public function setCustomSkin(CustomSkin $customSkin): void
    {
        $this->customSkin = $customSkin;
    }

    /**
     * @return string
     */
    public function getCustomCss(): string
    {
        return $this->customCss;
    }

    /**
     * @param string $customCss
     */
    public function setCustomCss(string $customCss): void
    {
        $this->customCss = $customCss;
    }

    /**
     * @return NormalizedVariableCollection
     */
    public function getVariableCollection(): NormalizedVariableCollection
    {
        return $this->variableCollection;
    }

    /**
     * @param NormalizedVariableCollection $variableCollection
     */
    public function setVariableCollection(NormalizedVariableCollection $variableCollection): void
    {
        $this->variableCollection = $variableCollection;
    }





}

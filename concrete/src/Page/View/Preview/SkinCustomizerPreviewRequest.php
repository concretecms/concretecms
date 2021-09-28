<?php
namespace Concrete\Core\Page\View\Preview;

class SkinCustomizerPreviewRequest extends SkinPreviewRequest
{

    /**
     * If we're previewing a custom skin's modification slike setting a new color on a link, changing a font family,
     * we're going to have customCss set in this parameter
     *
     * @var string|null
     */
    protected $customCss;
    
    /**
     * @return string|null
     */
    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    /**
     * @param string|null $customCss
     */
    public function setCustomCss(?string $customCss): void
    {
        $this->customCss = $customCss;
    }


}

<?php
namespace Concrete\Core\Page\View\Preview;

class ThemeCustomizerRequest implements PreviewRequestInterface
{

    /**
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

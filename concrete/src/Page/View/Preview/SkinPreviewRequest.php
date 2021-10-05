<?php
namespace Concrete\Core\Page\View\Preview;

use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class SkinPreviewRequest implements PreviewRequestInterface
{

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @return SkinInterface
     */
    public function getSkin(): ?SkinInterface
    {
        return $this->skin;
    }

    /**
     * @param SkinInterface $skin
     */
    public function setSkin(SkinInterface $skin): void
    {
        $this->skin = $skin;
    }


}

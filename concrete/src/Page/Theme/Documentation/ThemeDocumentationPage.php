<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Theme\Documentation\Traits\GetThemeContentXmlElementTrait;
use Concrete\Core\Page\Theme\Theme;

class ThemeDocumentationPage extends AbstractDocumentationContentPage
{
    use GetThemeContentXmlElementTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $contentFile;

    /**
     * @param Theme $theme
     * @param string $name
     * @param string $contentFile
     */
    public function __construct(Theme $theme, string $name, string $contentFile)
    {
        $this->name = $name;
        $this->theme = $theme;
        $this->contentFile = $contentFile;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getContentXmlElement(): ?\SimpleXMLElement
    {
        if ($this->contentFile) {
            return self::getThemeContentXmlElement($this->theme, $this->contentFile);
        }
        return null;
    }

    /**
     * @param string $contentFile
     */
    public function setContentFile(string $contentFile): void
    {
        $this->contentFile = $contentFile;
    }
}
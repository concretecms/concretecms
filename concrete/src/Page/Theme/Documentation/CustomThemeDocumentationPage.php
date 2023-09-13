<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageStructureRoutine;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Documentation\Traits\GetThemeContentXmlElementTrait;
use Concrete\Core\Page\Theme\Theme;

class CustomThemeDocumentationPage implements CustomDocumentationPageInterface
{
    use GetThemeContentXmlElementTrait;

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
     * @param string $contentFile
     */
    public function __construct(Theme $theme, string $contentFile)
    {
        $this->theme = $theme;
        $this->contentFile = $contentFile;
    }

    public function installDocumentationPage(Page $parent)
    {
        $xmlElement = self::getThemeContentXmlElement($this->theme, $this->contentFile);
        $routine = new ImportPageStructureRoutine();
        $routine->import($xmlElement);
        $routine = new ImportPageContentRoutine();
        $routine->import($xmlElement);
    }

    /**
     * @param string $contentFile
     */
    public function setContentFile(string $contentFile): void
    {
        $this->contentFile = $contentFile;
    }
}
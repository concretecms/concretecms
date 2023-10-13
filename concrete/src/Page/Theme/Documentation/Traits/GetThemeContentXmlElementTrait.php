<?php
namespace Concrete\Core\Page\Theme\Documentation\Traits;

use Concrete\Core\Page\Theme\Theme;

trait GetThemeContentXmlElementTrait
{
    /**
     * @return \SimpleXMLElement
     */
    public static function getThemeContentXmlElement(Theme $theme, string $contentFile): ?\SimpleXMLElement
    {
        return simplexml_load_file(
            $theme->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEME_DOCUMENTATION .
            DIRECTORY_SEPARATOR .
            $contentFile
        );
    }
}
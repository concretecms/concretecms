<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Theme\Theme;

class BedrockDocumentationPage extends AbstractDocumentationContentPage
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $contentFile;

    /**
     * @param string $name
     * @param string $contentFile
     */
    public function __construct(string $name, string $contentFile)
    {
        $this->name = $name;
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
        $file = $this->contentFile;
        return simplexml_load_file(
            DIR_BASE_CORE .
            DIRECTORY_SEPARATOR .
            DIRNAME_BEDROCK .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEME_DOCUMENTATION .
            DIRECTORY_SEPARATOR .
            $file
        );
    }

}
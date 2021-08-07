<?php
namespace Concrete\Core\Page\Theme\Documentation;

interface DocumentationPageInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return \SimpleXMLElement
     */
    public function getContentXmlElement(): \SimpleXMLElement;

}
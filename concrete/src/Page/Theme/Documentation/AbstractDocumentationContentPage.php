<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageContentRoutine;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Type;

abstract class AbstractDocumentationContentPage implements DocumentationPageInterface
{

    /**
     * @return \SimpleXMLElement|null
     */
    abstract public function getContentXmlElement(): ?\SimpleXMLElement;

    public function getDocumentationPageTypeHandle(): string
    {
        return THEME_DOCUMENTATION_PAGE_TYPE;
    }

    public function getDocumentationPageTemplateHandle(): string
    {
        return THEME_DOCUMENTATION_PAGE_TEMPLATE;
    }

    public function installDocumentationPage(Page $parent): Page
    {
        $type = Type::getByHandle($this->getDocumentationPageTypeHandle());
        $template = Template::getByHandle($this->getDocumentationPageTemplateHandle());
        $page = $parent->add($type, ['name' => $this->getName()], $template);
        $routine = new ImportPageContentRoutine();
        $element = $this->getContentXmlElement();
        if ($element) {
            $routine->importPageAreas($page, $element);
        }
        return $page;
    }

}
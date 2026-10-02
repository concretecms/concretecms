<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Page;

interface DocumentationPageInterface extends CustomDocumentationPageInterface
{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Theme\Documentation\CustomDocumentationPageInterface::installDocumentationPage()
     *
     * @return \Concrete\Core\Page\Page the installed page
     */
    public function installDocumentationPage(Page $parent);

    /**
     * @return string
     */
    public function getName(): string;


}
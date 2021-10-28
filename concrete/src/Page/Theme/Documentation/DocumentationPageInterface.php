<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Page;

interface DocumentationPageInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return void
     */
    public function installDocumentationPage(Page $parent);


}
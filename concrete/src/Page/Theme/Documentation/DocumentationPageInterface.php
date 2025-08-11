<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Page;

interface DocumentationPageInterface extends CustomDocumentationPageInterface
{

    /**
     * @return string
     */
    public function getName(): string;


}
<?php
namespace Concrete\Core\Page\Theme\Documentation;

interface DocumentationProviderInterface
{

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array;

}

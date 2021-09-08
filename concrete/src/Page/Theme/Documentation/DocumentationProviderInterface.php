<?php
namespace Concrete\Core\Page\Theme\Documentation;

interface DocumentationProviderInterface
{

    public function clearSupportingElements(): void;

    public function installSupportingElements(): void;

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array;


}

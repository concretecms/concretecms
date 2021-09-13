<?php
namespace Concrete\Core\Page\Theme\Documentation;

interface DocumentationProviderInterface
{

    public function clearSupportingElements(): void;

    public function installSupportingElements(): void;

    public function finishInstallation(): void;

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array;


}

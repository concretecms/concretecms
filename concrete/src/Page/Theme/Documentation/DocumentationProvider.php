<?php
namespace Concrete\Core\Page\Theme\Documentation;

class DocumentationProvider implements DocumentationProviderInterface
{

    public function clearSupportingElements(): void
    {
    }

    public function installSupportingElements(): void
    {
    }

    public function finishInstallation(): void
    {
    }

    public static function createFromArray($pages): DocumentationProvider
    {
        $provider = new self();
        foreach ($pages as $object) {
            $provider->addPage($object);
        }
        return $provider;
    }

    public function addPage(DocumentationPageInterface $page)
    {
        $this->pages[] = $page;
    }

    /**
     * @var DocumentationPageInterface[]
     */
    protected $pages;

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }



}

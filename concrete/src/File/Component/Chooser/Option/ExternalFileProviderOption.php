<?php

namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider;
use Concrete\Core\File\Component\Chooser\ExternalFileProviderOptionTrait;
use Concrete\Core\File\Component\Chooser\UploaderOptionInterface;

class ExternalFileProviderOption implements UploaderOptionInterface
{
    use ExternalFileProviderOptionTrait;

    protected $externalFileProvider;

    /**
     * ExternalFileProviderOption constructor.
     * @param ExternalFileProvider $externalFileProvider
     */
    public function __construct($externalFileProvider)
    {
        $this->externalFileProvider = $externalFileProvider;
    }

    public function getId()
    {
        return $this->externalFileProvider->getID();
    }

    public function getComponentKey(): string
    {
        return 'external-file-provider';
    }

    public function getTitle(): string
    {
        return $this->externalFileProvider->getDisplayName('text');
    }

}
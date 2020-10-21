<?php
namespace Concrete\Core\File\Component\Chooser;

trait ExternalFileProviderOptionTrait
{

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'componentKey' => $this->getComponentKey(),
            'title' => $this->getTitle(),
            'data' => [
                'typeHandle' => $this->externalFileProvider->getTypeObject()->getHandle(),
                'name' => $this->externalFileProvider->getName(),
                'supportFileTypes' => $this->externalFileProvider->getConfigurationObject()->supportFileTypes(),
                'hasCustomImportHandler' => $this->externalFileProvider->getConfigurationObject()->hasCustomImportHandler(),
            ],
        ];
    }

}
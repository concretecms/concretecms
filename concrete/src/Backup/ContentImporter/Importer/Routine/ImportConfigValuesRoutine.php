<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Package;
use SimpleXMLElement;

class ImportConfigValuesRoutine extends AbstractRoutine
{
    private $repositoryInstances;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'config_values';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::import()
     */
    public function import(SimpleXMLElement $sx)
    {
        if (!isset($sx->config)) {
            return;
        }
        $this->repositoryInstances = [];
        foreach ($sx->config->children() as $element) {
            $package = isset($element['package']) ? static::getPackageObject($element['package']) : null;
            $elementName = $element->getName();
            if ($elementName === 'option') {
                $key = (string) $element['name'];
            } else {
                // legacy
                $key = $elementName;
            }
            $value = (string) $element;
            $isJson = isset($element['json']) ? filter_var((string) $element['json'], FILTER_VALIDATE_BOOLEAN) : false;
            if ($isJson) {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } elseif ($value === 'false') { // deprecated
                $value = false;
            }
            $rawOverwrite = isset($element['overwrite']) ? (string) $element['overwrite'] : '';
            $overwrite = $rawOverwrite === '' ? true : filter_var($rawOverwrite, FILTER_VALIDATE_BOOLEAN);
            $repository = $this->getRepository(isset($element['storage']) ? (string) $element['storage'] : '', $package);
            if ($overwrite || !$repository->has($key)) {
                $repository->set($key, $value);
                $repository->save($key, $value);
            }
        }
    }

    /**
     * @return \Concrete\Core\Config\Repository\Repository|\Concrete\Core\Config\Repository\Liaison
     */
    private function getRepository(string $storage, ?Package $package)
    {
        switch ($storage) {
            case 'database':
                break;
            default:
                $storage = 'file';
                break;
        }
        $key = $storage . ($package ? "@{$package->getPackageHandle()}" : '');
        if (isset($this->repositoryInstances[$key])) {
            return $this->repositoryInstances[$key];
        }
        switch ($storage) {
            case 'database':
                $repository = $package ? $package->getController()->getDatabaseConfig() : app('config/database');
                break;
            case 'file':
                $repository = $package ? $package->getController()->getFileConfig() : app('config');
        }
        $this->repositoryInstances[$key] = $repository;

        return $repository;
    }
}

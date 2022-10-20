<?php

namespace Concrete\Core\Api\OpenApi;

class SourceRegistry
{

    /**
     * @var string[]
     */
    protected $sources = [];

    public function addDefaultSources()
    {
        $this->sources[] = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Api/Controller';
        $this->sources[] = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Api/Model';
        $this->sources[] = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Api/Response';
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function addSource(string $directory)
    {
        $this->sources[] = $directory;
    }

    public function addSources(array $directories)
    {
        foreach ($directories as $directory) {
            $this->addSource($directory);
        }
    }


}

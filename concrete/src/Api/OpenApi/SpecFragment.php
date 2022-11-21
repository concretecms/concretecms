<?php

namespace Concrete\Core\Api\OpenApi;

class SpecFragment
{

    /**
     * @var SpecPathCollection
     */
    protected $pathCollection;

    /**
     * @var SpecComponents|null
     */
    protected $components;

    /**
     * @var SpecSecurityScheme[]
     */
    protected $securitySchemes = [];

    public function addPath(SpecPath $path)
    {
        if (!isset($this->pathCollection)) {
            $this->pathCollection = new SpecPathCollection();
        }
        $this->pathCollection->add($path);
    }

    public function setComponents(SpecComponents $components)
    {
        $this->components = $components;
    }

    public function getPaths()
    {
        return $this->pathCollection;
    }

    /**
     * @return SpecModel[]
     */
    public function getComponents(): ?SpecComponents
    {
        return $this->components;
    }

    public function addSecurityScheme(SpecSecurityScheme $securityScheme)
    {
        $this->securitySchemes[] = $securityScheme;
    }

    /**
     * @return SpecSecurityScheme[]
     */
    public function getSecuritySchemes(): array
    {
        return $this->securitySchemes;
    }


}

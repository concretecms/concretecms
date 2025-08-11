<?php
namespace Concrete\Core\SiteInformation;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;

class DatabaseConfigSaver implements SaverInterface
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $fieldKeys = [];

    public function __construct(string $namespace, array $fieldKeys, Application $app)
    {
        $this->namespace = $namespace;
        $this->fieldKeys = $fieldKeys;
        $this->config = $app->make('config/database');
    }

    public function saveFromRequest(Request $request)
    {
        foreach ($this->fieldKeys as $fieldKey) {
            $key = sprintf('app.site_information.%s.%s', $this->namespace, $fieldKey);
            $this->config->save($key, $request->request->get($fieldKey));
        }
    }

    public function getResults(): array
    {
        return $this->config->get(sprintf('app.site_information.%s', $this->namespace)) ?? [];
    }

}

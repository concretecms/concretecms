<?php

namespace Concrete\Core\Api\Command;

use Concrete\Core\Api\OpenApi\SpecGenerator;
use Concrete\Core\Database\Connection\Connection;

class SynchronizeScopesCommandHandler
{

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var SpecGenerator
     */
    protected $specGenerator;

    public function __construct(Connection $db, SpecGenerator $specGenerator)
    {
        $this->db = $db;
        $this->specGenerator = $specGenerator;
    }

    public function __invoke(SynchronizeScopesCommand $command)
    {
        $this->db->executeStatement('delete from OAuth2Scope');
        $spec = $this->specGenerator->getSpec();
        $schemes = $spec->components->securitySchemes;
        foreach ($schemes as $scheme) {
            $scopes = $scheme->flows[0]->scopes;
            foreach ($scopes as $scope => $description) {
                $this->db->insert('OAuth2Scope', ['identifier' => $scope, 'description' => $description]);
            }
        }
    }

}

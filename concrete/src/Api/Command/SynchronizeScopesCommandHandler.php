<?php

namespace Concrete\Core\Api\Command;

use Concrete\Core\Api\OpenApi\SpecGenerator;
use Concrete\Core\Entity\OAuth\Scope;
use Doctrine\ORM\EntityManager;

class SynchronizeScopesCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var SpecGenerator
     */
    protected $specGenerator;

    public function __construct(EntityManager $entityManager, SpecGenerator $specGenerator)
    {
        $this->entityManager = $entityManager;
        $this->specGenerator = $specGenerator;
    }

    public function __invoke(SynchronizeScopesCommand $command)
    {
        $existingScopes = [];
        foreach ($this->entityManager->getRepository(Scope::class)->findAll() as $existingScope) {
            $existingScopes[] = $existingScope->getIdentifier();
        }

        // Now, find scopes from the generator.
        $descriptionScopes = [];
        $spec = $this->specGenerator->getSpec();
        $schemes = $spec->components->securitySchemes;
        foreach ($schemes as $scheme) {
            $scopes = $scheme->flows[0]->scopes;
            foreach ($scopes as $scope => $description) {
                $descriptionScopes[$scope] = $description;
            }
        }

        // Now, let's add new scopes that aren't present.
        foreach ($descriptionScopes as $scope => $description) {
            if (!in_array($scope, $existingScopes)) {
                // The scope in the generated array is NOT in the existing scopes. So it's new. Let's add it.
                $this->entityManager
                    ->getConnection()
                    ->insert('OAuth2Scope', ['identifier' => $scope, 'description' => $description]);
            }

            $key = array_search($scope, $existingScopes);
            if ($key !== false) {
                unset($existingScopes[$key]);
            }
        }

        // Now any scopes that are in the existing scopes array are old scopes that should no longer
        // be there.
        foreach ($existingScopes as $scopeToDelete) {
            $this->entityManager
                ->getConnection()
                ->delete('OAuth2Scope', ['identifier' => $scopeToDelete]);
        }
    }

}

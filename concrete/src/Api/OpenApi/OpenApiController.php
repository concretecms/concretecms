<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\ApiController;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\OAuth\Scope;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class OpenApiController extends ApiController
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Application $app, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    /**
     * Outputs the OpenAPI spec, substituting local URL values for placeholders.
     * @TODO - make this modular, so that third party packages can add to the API and it shows up here.
     * @return JsonResponse
     */
    public function outputApiSpec()
    {
        $json = file_get_contents(
            DIR_BASE_CORE .
            DIRECTORY_SEPARATOR .
            DIRNAME_CONFIG .
            DIRECTORY_SEPARATOR . 'openapi.json'
        );
        $scopeRepository = $this->entityManager->getRepository(Scope::class);
        $url = rtrim($this->app->make('url/canonical'), '/');
        $apiUrl = $url . '/ccm/api/v1';
        $data = json_decode($json);

        // Load servers into it properly.
        $servers = ['url' => $apiUrl];
        $data->servers = [$servers];

        // Load the OAuth2 information in properly
        $scopes = [];
        foreach ($scopeRepository->findAll() as $scope) {
            $scopes[$scope->getIdentifier()] = $scope->getDescription();
        }
        $securitySchemes = [
            'oAuth2' => [
                'type' => 'oauth2',
                'flows' => [
                    'clientCredentials' => [
                        'tokenUrl' => $url . '/oauth/2.0/token',
                        'scopes' => $scopes,
                    ],
                    'authorizationCode' => [
                        'tokenUrl' => $url . '/oauth/2.0/token',
                        'authorizationUrl' => $url . '/oauth/2.o/authorize',
                        'scopes' => $scopes,
                    ]
                ]
            ]

        ];
        $data->components->securitySchemes = $securitySchemes;
        return new JsonResponse($data);
    }

}

<?php
namespace Concrete\Core\API;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\Router;
use Concrete\Core\Http\Middleware\OAuthMiddleware;

class APIServiceProvider extends ServiceProvider
{

    protected $router;

    public function __construct(Router $router, Application $app)
    {
        $this->router = $router;
        parent::__construct($app);
    }

    public function register()
    {

        $this->app->singleton('api', function ($app) {
            return $app->make('Concrete\Core\API\ClientFactory');
        });

        $this->router->buildGroup()
            ->setPrefix('/ccm/api/v1')
            ->addMiddleware(OauthMiddleware::class)
            ->routes(function(Router $groupRouter) {
                $groupRouter->get('/hello', function() {
                    return json_encode(['hello' => t('Hello')]);
                });
            });
    }

}
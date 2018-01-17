<?php
namespace Concrete\Core\API;

use Concrete\Core\API\Transformer\InfoTransformer;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\Router;
use Concrete\Core\System\Info;
use Concrete\Core\Http\Middleware\ProjectorMiddleware;
use League\Fractal\Resource\Item;
use Concrete\Core\Http\Middleware\APIAuthenticatorMiddleware;
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
    }

}
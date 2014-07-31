<?php
namespace Concrete\Core\Search;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class PaginationServiceProvider extends ServiceProvider {

    public function register() {
        $singletons = array(
            'pagination/view' => '\Concrete\Core\Search\Pagination\View\ConcreteBootstrap3View'
        );

        foreach($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }
    }


}
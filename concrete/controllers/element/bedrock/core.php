<?php
namespace Concrete\Controller\Element\Bedrock;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Search\Pagination\View\Manager;
use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;

class Core extends Colors
{
    
    public function getElement()
    {
        return 'bedrock/core';
    }

    public function view()
    {
        parent::view();
        $this->set('paginationCallable', function() {
            $driver = $this->app->make(Manager::class)->driver('application');
            $pagination = new Pagerfanta(new NullAdapter(5));
            $pagination->setMaxPerPage(1);
            $pagination->setCurrentPage(3);
            print $driver->render($pagination, function() {
                return '';
            });
        });


    }
}

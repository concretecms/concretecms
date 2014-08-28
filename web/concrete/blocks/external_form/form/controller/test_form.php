<?php
namespace Concrete\Block\ExternalForm\Form\Controller;
use Concrete\Core\Controller\AbstractController;

class TestForm extends AbstractController
{

    public function action_test_search()
    {
        $this->set('response', t('Thanks!'));
        return true;
    }

    public function view()
    {
        $this->set('message', t('This is just an example of how a custom form works.'));
    }
}

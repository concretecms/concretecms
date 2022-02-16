<?php

namespace Concrete\Block\ExternalForm\Form\Controller;

use Concrete\Core\Controller\AbstractController;

class TestForm extends AbstractController
{
    /**
     * This should be public to allow the express form to set the blockID.
     *
     * @var int|null
     */
    public $bID;

    /**
     * @var string[]
     */
    protected $helpers = ['form'];

    /**
     * @param int|bool $bID
     *
     * @return bool|void
     */
    public function action_test_search($bID = false)
    {
        if ($this->bID == $bID) {
            $this->set('response', t('Thanks!'));
            $this->view();

            return true;
        }
    }

    /**
     * @return void
     */
    public function view()
    {
        $this->set('message', t('This is just an example of how a custom form works.'));
    }
}

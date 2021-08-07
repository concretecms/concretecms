<?php
namespace Concrete\Controller\Element\Bedrock;

use Concrete\Core\Controller\ElementController;

class Colors extends ElementController
{
    
    public function getElement()
    {
        return 'bedrock/colors';
    }

    public function view()
    {
        $this->set('colors', [

            ['primary', t('Primary')],
            ['secondary', t('Secondary')],
            ['success', t('Success')],
            ['danger', t('Danger')],
            ['warning', t('Warning')],
            ['info', t('Info')],
            ['dark', t('Dark')],
            ['light', t('Light'), 'text-dark'],

        ]);
    }

}

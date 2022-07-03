<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Application\Application;
use Concrete\Core\Form\Service\Form;

class Password
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }    

    /**
     * Creates form fields and JavaScript includes to add a password field with password show toggle.
     *
     * @param string $inputName
     * @param string|null $value
     * @param array $toggler
     */
    public function output($inputName, $value = null, $toggler = true)
    {
        $form = $this->app->make(Form::class);

        $html = '';
        $html .= $toggler ? '<div class="input-group">' : '';
        $html .= $form->password($inputName, ['autocomplete' => 'off']);
        $html .= $toggler ? '<button type="button" class="input-group-icon btn-toggle-password-visibility"><i class="fas fa-eye" aria-hidden="true"></i></button>' : '';
        $html .= $toggler ? '</div>' : '';

        return $html;
    }
}

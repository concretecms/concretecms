<?php
namespace Concrete\Controller\Element\Dashboard\Express;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

class Forms extends ElementController
{
    protected $forms;
    protected $currentForm;
    protected $url;
    public function __construct(Entity $entity, $url, Form $currentForm)
    {
        parent::__construct();

        if($entity != null){
          $this->forms = $entity->getForms();
          $this->currentForm = $currentForm;
        }
        $this->url = $url;
    }

    public function getElement()
    {
        return 'dashboard/express/forms';
    }

    public function view()
    {
        $this->set('forms', $this->forms);
        $this->set('currentForm',$this->currentForm);
        $this->set('url',$this->url);
    }
}

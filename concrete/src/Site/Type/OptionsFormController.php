<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\UserInterface\OptionsForm\OptionsFormControllerInterface;
use Concrete\Core\Application\UserInterface\OptionsForm\OptionsFormProviderInterface;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Site\Type;

abstract class OptionsFormController extends ElementController implements OptionsFormControllerInterface
{

    /**
     * @var $type Type
     */
    protected $type;

    /**
     * @param $provider OptionsFormProvider
     */
    public function setupController(OptionsFormProviderInterface $provider)
    {
        $this->type = $provider->getType();
    }

}

<?php
namespace Concrete\Core\StyleCustomizer\Customizer\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\StyleCustomizer\Customizer\Type\TypeInterface as CustomizerTypeInterface;

abstract class AbstractCustomizerType implements CustomizerTypeInterface
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * AbstractCustomizerType constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


}

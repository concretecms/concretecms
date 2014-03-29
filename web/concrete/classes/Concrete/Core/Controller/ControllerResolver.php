<?

namespace Concrete\Core\Controller;
use Concrete\Core\Foundation\ClassLoader;

class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver {

    protected function createController($controller) {

        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        // now we do some concrete5 magic to route the controller into the right name space.
        $cl = ClassLoader::getInstance();
        $class = $cl->getClassName('Controller\\' . $class);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return array(new $class(), $method);
    }


}
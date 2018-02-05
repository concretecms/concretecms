<?php

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Event\PackageEntities as PackageEntitiesEvent;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Throwable;

class MetadataGenerator
{
    public function getAllBindings()
    {
        $bindings = [];
        $app = ApplicationFacade::getFacadeApplication();

        foreach ($app->getBindings() as $name => $binding) {
            $className = $this->resolveAbstractToClassName($app, $name);
            if ($className !== null) {
                $bindings[$name] = $className;
            }
        }
        foreach ($app->getRegisteredAliases() as $alias) {
            if (!isset($bindings[$alias])) {
                $className = $this->resolveAbstractToClassName($app, $alias);
                if ($className !== null) {
                    $bindings[$alias] = $className;
                }
            }
        }
        foreach ($app->getRegisteredInstances() as $instance) {
            if (!isset($bindings[$instance])) {
                $className = $this->resolveAbstractToClassName($app, $instance);
                if ($className !== null) {
                    $bindings[$instance] = $className;
                }
            }
        }

        return $bindings;
    }

    /**
     * Return the list of custom entity manager repositories.
     *
     * @return array array keys are the entity fully-qualified class names, values are the custom repository fully-qualified class names
     */
    public function getCustomEntityRepositories()
    {
        $result = [];
        $app = ApplicationFacade::getFacadeApplication();
        $pev = new PackageEntitiesEvent();
        $app->make('director')->dispatch('on_list_package_entities', $pev);
        $entityManagers = array_merge([$app->make(EntityManagerInterface::class)], $pev->getEntityManagers());
        foreach ($entityManagers as $entityManager) {
            /* @var EntityManagerInterface $entityManager */
            $metadataFactory = $entityManager->getMetadataFactory();
            foreach ($metadataFactory->getAllMetadata() as $metadata) {
                /* @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata */
                $entityClassName = $metadata->getName();
                $entityRepository = $entityManager->getRepository($entityClassName);
                $entityRepositoryClassName = get_class($entityRepository);
                switch ($entityRepositoryClassName) {
                    case \Doctrine\ORM\EntityRepository::class:
                        break;
                    default:
                        $result[$entityClassName] = $entityRepositoryClassName;
                        break;
                }
            }
        }

        return $result;
    }

    public function render()
    {
        $output = [
            '<?php',
            '',
            'namespace PHPSTORM_META;',
            '',
            "die('Access Denied.');",
            '',
        ];

        // Define $app->build('');
        $output = array_merge($output, $this->getOverride('\Illuminate\Contracts\Container\Container::build(0)', ['' => "'@'"], '$app->build(SomeClass::class)'));

        // Define $app->make('');
        $bindings = $this->getAllBindings();
        ksort($bindings);

        $makeMethod = [
            '' => "'@'",
        ];

        foreach ($bindings as $name => $className) {
            $makeMethod[$name] = "\\{$className}::class";
        }

        $output = array_merge($output, $this->getOverride('\Illuminate\Contracts\Container\Container::make(0)', $makeMethod, '$app->make(\'something\') or $app->make(SomeClass::class)'));
        $output = array_merge($output, $this->getOverride('new \Illuminate\Contracts\Container\Container', $makeMethod, '$app[SomeClass::class]'));

        $customEntityRepositories = $this->getCustomEntityRepositories();
        if (!empty($customEntityRepositories)) {
            ksort($customEntityRepositories);
            $getRepositoryMethod = [];
            foreach ($customEntityRepositories as $entityClass => $repositoryClass) {
                $getRepositoryMethod[$entityClass] = "\\{$repositoryClass}::class";
            }
            $output = array_merge($output, $this->getOverride('\Doctrine\ORM\EntityManagerInterface::getRepository(0)', $getRepositoryMethod, '$em->getRepository(EntityClass::class)'));
        }
        $output = array_merge($output, $this->getOverride('\Doctrine\ORM\EntityManagerInterface::find(0)', ['' => "'@'"], '$em->find(EntityClass::class, $id)'));

        return implode("\n", $output);
    }

    /**
     * @param Application $app
     * @param string $abstract
     *
     * @return string|null
     */
    private function resolveAbstractToClassName(Application $app, $abstract)
    {
        $result = null;
        try {
            $instance = $app->make($abstract);
            if (is_object($instance)) {
                $className = get_class($instance);
                if (ltrim($abstract, '\\') !== $className) {
                    $result = $className;
                }
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }

        return $result;
    }

    private function getOverride($string, $makeMethod, $comment)
    {
        $output = [
            "// {$comment}",
            "override({$string}, map([",
        ];

        foreach ($makeMethod as $name => $className) {
            $output[] = "  '{$name}' => {$className},";
        }

        $output[] = ']));';
        $output[] = '';

        return $output;
    }
}

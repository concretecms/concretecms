<?php

namespace Concrete\Core\Permission\Category;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Filesystem\FileLocator;

class TaskLauncher extends Controller
{
    public function launch(string $categoryHandle, string $task): Response
    {
        $this->checkCSRF($categoryHandle, $task);
        $category = $this->getCategory($categoryHandle);
        $handler = $this->getTaskHandler($category);
        $response = $this->runHandler($handler, $task) ?: $this->renderHandler($handler, $task, (string) $category->getPackageHandle());
        if ($response === null) {
            throw new UserMessageException(t("The task handler did't return a Response and doesn't implement a view"));
        }

        return $response;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(string $categoryHandle, string $task): void
    {
        $token = $this->app->make(Token::class);
        if (!$token->validate("{$task}@{$categoryHandle}")) {
            throw new UserMessageException($token->getErrorMessage());
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getTaskHandler(Category $category): TaskHandlerInterface
    {
        $path = '/permissions/categories/task_handlers/' . $category->getPermissionKeyCategoryHandle();
        $packageHandle = (string) $category->getPackageHandle();
        $locator = $this->app->make(FileLocator::class);
        if ($packageHandle !== '') {
            $locator->addLocation(new FileLocator\PackageLocation($packageHandle));
        }
        $r = $locator->getRecord($path . '.php');
        $prefix = $r->override ? true : $packageHandle;
        $className = core_class('Controller\\' . str_replace('/', '\\', camelcase($path, true)), $prefix);
        if (!class_exists($className)) {
            throw new UserMessageException(t('Unable to find the class %s', $className));
        }
        if (!is_a($className, TaskHandlerInterface::class, true)) {
            throw new UserMessageException(t('The class %1$s does not implement the interface %2$s', $className, TaskHandlerInterface::class));
        }

        return $this->app->make($className);
    }

    protected function runHandler(TaskHandlerInterface $handler, string $task): ?Response
    {
        return $handler->handle($task, $this->request->request->all() + $this->request->query->all());
    }

    protected function renderHandler(TaskHandlerInterface $handler, string $task, string $packageHandle): ?Response
    {
        $viewPath = $handler->getViewPath();
        if ($viewPath === '') {
            return null;
        }
        $view = new View($viewPath);
        $view->setPackageHandle($packageHandle);
        $view->setController($handler);
        $content = $view->render();
        if ($content instanceof Response) {
            return $content;
        }

        return $this->app->make(ResponseFactoryInterface::class)->create($content);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getCategory(string $categoryHandle): Category
    {
        $category = Category::getByHandle($categoryHandle);
        if ($category === null) {
            throw new UserMessageException(t('Invalid permission key category.'));
        }

        return $category;
    }
}

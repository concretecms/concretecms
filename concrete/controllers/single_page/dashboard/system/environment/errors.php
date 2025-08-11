<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Error\Handling\PhpErrors;
use Concrete\Core\Logging\Levels;
use Concrete\Core\Page\Controller\DashboardPageController;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;

class Errors extends DashboardPageController
{
    public function view()
    {
        $phpErrors = $this->app->make(PhpErrors::class);
        $config = $this->app->make('config');
        $logLevels = [];
        foreach (Logger::getLevels() as $key => $value) {
            $logLevels[] = [
                'level' => Logger::toMonologLevel($key),
                'name' => $key,
                'displayName' => Levels::getLevelDisplayName($value),
            ];
        }
        $minimumLogLevel = 0;
        $minimumLogLevelName = null;
        if ($config->get('concrete.log.configuration.mode') === 'simple') {
            $minimumLogLevel = Logger::toMonologLevel($config->get('concrete.log.configuration.simple.core_logging_level'));
            $minimumLogLevelName = Levels::getLevelDisplayName($minimumLogLevel);
        }
        $this->set('minimumLogLevel', $minimumLogLevel);
        $this->set('minimumLogLevelName', $minimumLogLevelName);
        $this->set('phpErrors', $phpErrors);
        $this->set('logLevels', $logLevels);
        $this->set('errorConfiguration', $config->get('concrete.error.handling'));
        $this->set('errorDisplay', $config->get('concrete.error.display.guests'));
        $this->set('errorDisplayPrivileged', $config->get('concrete.error.display.privileged'));
    }

    public function preview()
    {
        $config = $this->app->make('config');
        $config->set('concrete.error.display.guests', $this->request->query->get('errorDisplay'));
        $config->set('concrete.error.display.privileged', $this->request->query->get('errorDisplay'));
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'debug_example';
        throw new class('Sample Output!') extends \Exception {};
    }

    public function submit()
    {
        $config = $this->app->make('config');
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $configuration = json_decode($this->request->request->get('errorConfiguration'), true);
            if (is_array($configuration)) {
                $config->save('concrete.error.handling', $configuration);
                $config->save('concrete.error.display.guests', $this->request->request->get('errorDisplay'));
                $config->save('concrete.error.display.privileged', $this->request->request->get('errorDisplayPrivileged'));
                $this->flash('success', t('Error handling configuration saved.'));
                return new JsonResponse(['saved' => true]);
            } else {
                $this->error->add(t('Unknown error occurred when attempting to save error handling configuration.'));
            }
        }

        return new JsonResponse($this->error);
    }
}

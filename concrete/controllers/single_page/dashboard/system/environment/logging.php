<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RedirectResponse;
use Config;
use Illuminate\Filesystem\Filesystem;
use Loader;

class Logging extends DashboardPageController
{
    /**
     * Dasboard page view.
     *
     * @param string $strStatus - Result of attempting to update logging settings
     */
    public function view($strStatus = false)
    {
        $config = $this->app->make('config');
        $strStatus = (string) $strStatus;
        $intLogErrors = (int) $config->get('concrete.log.errors');
        $intLogEmails = (int) $config->get('concrete.log.emails');
        $intLogApi = (int) $config->get('concrete.log.api');

        $this->set('fh', Loader::helper('form'));
        $this->set('intLogErrors', $intLogErrors);
        $this->set('intLogEmails', $intLogEmails);
        $this->set('intLogApi', $intLogApi);

        if ($strStatus == 'logging_saved') {
            $this->set('message', t('Logging configuration saved.'));
        }

        $levels = [
            'DEBUG' => t('Debug'),
            'INFO' => t('Info'),
            'NOTICE' => t('Notice'),
            'WARNING' => t('Warning'),
            'ERROR' => t('Error'),
            'CRITICAL' => t('Critical'),
            'ALERT' => t('Alert'),
            'EMERGENCY' => t('Emergency'),
        ];
        $handlers = [
            'database' => t('Database'),
            'file' => t('File'),
        ];

        $this->set('enableDashboardReport', !!$config->get('concrete.log.enable_dashboard_report'));
        $this->set('levels', $levels);
        $this->set('handlers', $handlers);
        $this->set('loggingMode', $config->get('concrete.log.configuration.mode'));
        $this->set('coreLoggingLevel', $config->get('concrete.log.configuration.simple.core_logging_level'));
        $this->set('handler', $config->get('concrete.log.configuration.simple.handler'));
        $this->set('logFile', $config->get('concrete.log.configuration.simple.file.file'));
    }

    /**
     * Updates logging settings.
     */
    public function update_logging()
    {
        $config = $this->app->make('config');
        $request = $this->request;

        if (!$this->token->validate('update_logging')) {
            return $this->showError($this->token->getErrorMessage());
        }

        // Load in variables from the request
        $mode = (string) $request->request->get('logging_mode') === 'advanced' ? 'advanced' : 'simple';
        $handler = $mode === 'simple' ? (string) $request->request->get('handler', 'database') : null;
        $logFile = $handler === 'file' ? (string) $request->request->get('logFile') : null;
        $enableDashboardReport = $request->request->get('enable_dashboard_report') ? true : false;
        $loggingLevel = strtoupper((string) $request->request->get('logging_level'));
        $intLogErrorsPost = (int) $request->request->has('ENABLE_LOG_ERRORS');
        $intLogEmailsPost = (int) $request->request->has('ENABLE_LOG_EMAILS');
        $intLogApiPost = (int) $request->request->has('ENABLE_LOG_API');


        // Handle 'file' based logging
        if ($handler === 'file') {
            $directory = dirname($logFile);

            // Validate the file name
            if (pathinfo($logFile, PATHINFO_EXTENSION) !== 'log') {
                return $this->showError(t('The filename provided must be a valid filename and end with .log'));
            }

            if (stripos($logFile, 'phar:') !== false) {
                return $this->showError(t('The filename provided must be a valid filename.'));
            }

            // Validate the file path, create the log file if needed
            if (!file_exists($logFile)) {
                // If the file doesn't exist, make sure we can create one
                if (!is_writable($directory) || !is_dir($directory)) {
                    return $this->showError(t('Log file does not exist on the server. The directory of the file provided must exist and be writable on the web server.'));
                }

                // Make sure we actually created the file
                if (!touch($logFile) || !file_exists($logFile)) {
                    return $this->showError(t('Unable to create log file. Please create the file manually and try again.'));
                }
            } else {
                // The file exists, let's make sure it's actually a file and can be written to
                if (!is_file($logFile)) {
                    return $this->showError(t('Log file exists but doesn\'t appear to be a file.'));
                }

                if (!is_writable($logFile)) {
                    return $this->showError(t('Log file exists but is not writable by the web server.'));
                }
            }
        }

        // Start saving stuff
        $config->save('concrete.log.errors', $intLogErrorsPost);
        $config->save('concrete.log.emails', $intLogEmailsPost);
        $config->save('concrete.log.api', $intLogApiPost);
        $config->save('concrete.log.enable_dashboard_report', $enableDashboardReport);
        $config->save('concrete.log.configuration.mode', $mode);

        // Save simple mode stuff
        $config->save('concrete.log.configuration.simple.file.file', $logFile);
        $config->save('concrete.log.configuration.simple.core_logging_level', $loggingLevel);
        $config->save('concrete.log.configuration.simple.handler', $handler);

        return new RedirectResponse($this->action('logging_saved'));
    }

    /**
     * Manage adding
     * @param $message
     */
    private function showError($message)
    {
        $this->error->add($message);
        $this->view();
    }
}

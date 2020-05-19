<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Illuminate\Filesystem\Filesystem;
use Loader;

class Logging extends DashboardPageController
{
    /**
     * Dashboard page view.
     *
     * @param string $strStatus - Result of attempting to update logging settings
     */
    public function view(string $strStatus = '')
    {
        $config = $this->app->make('config');
        if ($strStatus === 'logging_saved') {
            $this->set('success', t('Logging configuration saved.'));
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

        $this->set('levels', $levels);
        $this->set('handlers', $handlers);
        $this->set('enableDashboardReport', (bool) $config->get('concrete.log.enable_dashboard_report'));
        $this->set('intLogApi', (bool) $config->get('concrete.log.api'));
        $this->set('intLogEmails', (bool) $config->get('concrete.log.emails'));
        $this->set('intLogErrors', (bool) $config->get('concrete.log.errors'));
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
        if (!$this->token->validate('update_logging')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->request->request->get('handler') == 'file' && $this->request->request->get('logging_mode')) {
            $logFile = $this->request->request->get('logFile');
            $filesystem = new Filesystem();
            $directory = dirname($logFile);
            if ($filesystem->isFile($logFile) && !$filesystem->isWritable($logFile)) {
                $this->error->add(t('Log file exists but is not writable by the web server.'));
            }
            if (!$filesystem->isFile($logFile) && (!$filesystem->isDirectory($directory) || !$filesystem->isWritable($directory))) {
                $this->error->add(t('Log file does not exist on the server. The directory of the file provided must exist and be writable on the web server.'));
            }
            $filename = basename($logFile);
            if (!$filename || substr($filename, -4) != '.log') {
                $this->error->add(t('The filename provided must be a valid filename and end with .log'));
            }
        }
        if (!$this->error->has()) {
            $intLogErrorsPost = $this->post('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
            $intLogEmailsPost = $this->post('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;
            $intLogApiPost = $this->post('ENABLE_LOG_API') == 1 ? 1 : 0;

            $config->save('concrete.log.errors', $intLogErrorsPost);
            $config->save('concrete.log.emails', $intLogEmailsPost);
            $config->save('concrete.log.api', $intLogApiPost);

            $mode = $this->request->request->get('logging_mode');
            if ($mode != 'advanced') {
                $mode = 'simple';
                $config->save('concrete.log.configuration.simple.core_logging_level', $post->get('logging_level'));
                $config->save('concrete.log.configuration.simple.handler', $post->get('handler'));
                $config->save('concrete.log.configuration.simple.file.file', $post->get('logFile'));
            }

            $config->save('concrete.log.enable_dashboard_report', $post->get('enable_dashboard_report') ? true : false);
            $config->save('concrete.log.configuration.mode', $mode);

            $this->redirect('/dashboard/system/environment/logging', 'logging_saved');
        }
        $this->view();
    }
}

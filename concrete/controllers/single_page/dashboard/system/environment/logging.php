<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;
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
        $intLogErrors = $config->get('concrete.log.errors') == 1 ? 1 : 0;
        $intLogEmails = $config->get('concrete.log.emails') == 1 ? 1 : 0;
        $intLogApi = $config->get('concrete.log.api') == 1 ? 1 : 0;

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
                $config->save('concrete.log.configuration.simple.core_logging_level',
                    $this->request->request->get('logging_level')
                );
                $config->save('concrete.log.configuration.simple.handler',
                    $this->request->request->get('handler')
                );
                $config->save('concrete.log.configuration.simple.file.file',
                    $this->request->request->get('logFile')
                );
            }
            $config->save('concrete.log.enable_dashboard_report',
                $this->request->request->get('enable_dashboard_report') ? true : false);
            $config->save('concrete.log.configuration.mode', $mode);

            $this->redirect('/dashboard/system/environment/logging', 'logging_saved');
        }
        $this->view();
    }
}

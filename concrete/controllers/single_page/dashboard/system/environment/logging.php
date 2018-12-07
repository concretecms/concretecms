<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
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

        $this->set('fh', Loader::helper('form'));
        $this->set('intLogErrors', $intLogErrors);
        $this->set('intLogEmails', $intLogEmails);

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
        $this->set('levels', $levels);
        $this->set('loggingMode', $config->get('concrete.log.configuration.mode'));
        $this->set('coreLoggingLevel', $config->get('concrete.log.configuration.simple.core_logging_level'));
    }

    /**
     * Updates logging settings.
     */
    public function update_logging()
    {
        $config = $this->app->make('config');
        if ($this->token->validate('update_logging')) {
            if ($this->isPost()) {
                $intLogErrorsPost = $this->post('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
                $intLogEmailsPost = $this->post('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;

                $config->save('concrete.log.errors', $intLogErrorsPost);
                $config->save('concrete.log.emails', $intLogEmailsPost);

                $mode = $this->request->request->get('logging_mode');
                if ($mode != 'advanced') {
                    $mode = 'simple';
                    $config->save('concrete.log.configuration.simple.core_logging_level',
                        $this->request->request->get('logging_level')
                    );
                }
                $config->save('concrete.log.configuration.mode', $mode);

                $this->redirect('/dashboard/system/environment/logging', 'logging_saved');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }
}

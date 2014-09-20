<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;

class Debug extends DashboardPageController
{

    public function view()
    {
        $enabled = Config::get('concrete.debug.display_errors');
        $detail = Config::get('concrete.debug.detail');

        $this->set('debug_enabled', $enabled);
        $this->set('debug_detail', $detail);
    }

    public function update_debug()
    {
        if ($this->token->validate("update_debug")) {
            if ($this->isPost()) {
                Config::save('concrete.debug.detail', $this->post('debug_detail'));
                Config::save('concrete.debug.display_errors', !!$this->post('debug_enabled'));
                $this->redirect('/dashboard/system/environment/debug', 'debug_saved');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }

    public function debug_saved()
    {
        $this->set('message', t('Debug configuration saved.'));
        $this->view();
    }

    public function debug_example()
    {
        \Config::set('concrete.log.errors', false);
        \Config::set('concrete.debug.display_errors', true);
        \Config::set('concrete.debug.detail', 'debug');

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'debug_example';

        throw new ExampleException('Sample Debug Output!');
    }

    public function message_example()
    {
        \Config::set('concrete.log.errors', false);
        \Config::set('concrete.debug.display_errors', true);
        \Config::set('concrete.debug.detail', 'message');

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'debug_example';

        throw new ExampleException('Sample Message Output!');
    }

    public function disabled_example()
    {
        \Config::set('concrete.log.errors', false);
        \Config::set('concrete.debug.display_errors', false);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'debug_example';

        throw new ExampleException('Sample Disabled Output!');
    }

}

class ExampleException extends \Exception
{

}

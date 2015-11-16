<?php
namespace Concrete\Core\Session;

use Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler;
use Concrete\Core\Utility\IPAddress;
use Config;
use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use \Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Core;

class Session
{

    public static function start()
    {
        $app = Core::make('app');
        if ($app->isRunThroughCommandLineInterface()) {
            $storage = new MockArraySessionStorage();
        } else {
            if (Config::get('concrete.session.handler') == 'database') {
                $db = \Database::get();
                $storage = new NativeSessionStorage(array(),
                    new PdoSessionHandler($db->getWrappedConnection(), array(
                            'db_table' => 'Sessions',
                            'db_id_col' => 'sessionID',
                            'db_data_col' => 'sessionValue',
                            'db_time_col' => 'sessionTime'
                        )
                    )
                );
            } else {
                $savePath = Config::get('concrete.session.save_path') ?: null;
                $storage = new NativeSessionStorage(array(), new NativeFileSessionHandler($savePath));
            }
            $options = Config::get('concrete.session.cookie');
            if ($options['cookie_path'] === false) {
                $options['cookie_path'] = $app['app_relative_path'] . '/';
            }
            $options['gc_max_lifetime'] = Config::get('concrete.session.max_lifetime');
            $storage->setOptions($options);
        }

        $session = new SymfonySession($storage);
        $session->setName(Config::get('concrete.session.name'));
        return $session;
    }

    public static function testSessionFixation(SymfonySession $session)
    {
        $iph = Core::make('helper/validation/ip');
        $currentIp = $iph->getRequestIP();
        $ip = $session->get('CLIENT_REMOTE_ADDR');
        $agent = $session->get('CLIENT_HTTP_USER_AGENT');
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'];

        if (\Config::get('concrete.security.session.invalidate_on_ip_mismatch') && $ip && $ip != $currentIp->getIp(IPAddress::FORMAT_IP_STRING)) {
            \Log::warning(t('Session Invalidated. Session IP %s did not match provided IP %s.', $ip, $currentIp->getIp(IPAddress::FORMAT_IP_STRING)));
            $session->invalidate();
            return;
        }

        if (\Config::get('concrete.security.session.invalidate_on_user_agent_mismatch') && $agent && $agent != $currentUserAgent) {
            \Log::warning(t('Session Invalidated. Session user agent %s did not match provided agent %s', $agent, $currentUserAgent));
            $session->invalidate();
            return;
        }

        if (!$ip && $currentIp !== false) {
            $session->set('CLIENT_REMOTE_ADDR', $currentIp->getIp(IPAddress::FORMAT_IP_STRING));
        }
        if (!$agent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $session->set('CLIENT_HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        }
    }
}

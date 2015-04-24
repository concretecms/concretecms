<?php
namespace Concrete\Core\Session;

use Concrete\Core\Utility\IPAddress;
use Config;
use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
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
                $storage = new NativeSessionStorage(array(), new NativeFileSessionHandler());
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

        static::testSessionFixation($session);
        return $session;
    }

    protected static function testSessionFixation(SymfonySession $session)
    {
        $iph = Core::make('helper/validation/ip');
        $currentIp = $iph->getRequestIP();
        $ip = $session->get('CLIENT_REMOTE_ADDR');
        $agent = $session->get('CLIENT_HTTP_USER_AGENT');
        if ($ip && $ip != $currentIp->getIp(IPAddress::FORMAT_IP_STRING) || $agent && $agent != $_SERVER['HTTP_USER_AGENT']) {
            $session->invalidate();
        }
        if (!$ip && $currentIp !== false) {
            $session->set('CLIENT_REMOTE_ADDR', $currentIp->getIp(IPAddress::FORMAT_IP_STRING));
        }
        if (!$agent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $session->set('CLIENT_HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        }
    }
}

<?php 
namespace Concrete\Core\Session;
use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use \Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Concrete;

class Session {

	public static function start() {
		$app = Concrete::make('app');
		if ($app->isRunThroughCommandLineInterface()) {
			$storage = new MockArraySessionStorage();
		} else {
			$storage = new NativeSessionStorage();
			$options = array(
				'cookie_lifetime' => 0,
				'cookie_secure' => false,
				'cookie_httponly' => true,
				'gc_maxlifetime' => SESSION_MAX_LIFETIME
			);
			if (defined('SESSION_COOKIE_PARAM_PATH') && SESSION_COOKIE_PARAM_PATH) {
				$options['cookie_path'] = SESSION_COOKIE_PARAM_PATH;
			}
			if (defined('SESSION_COOKIE_PARAM_LIFETIME') && SESSION_COOKIE_PARAM_LIFETIME) {
				$options['cookie_lifetime'] = SESSION_COOKIE_PARAM_LIFETIME;
			}
			if (defined('SESSION_COOKIE_PARAM_DOMAIN') && SESSION_COOKIE_PARAM_DOMAIN) {
				$options['cookie_domain'] = SESSION_COOKIE_PARAM_DOMAIN;
			}
			if (defined('SESSION_COOKIE_PARAM_SECURE') && SESSION_COOKIE_PARAM_SECURE) {
				$options['cookie_secure'] = SESSION_COOKIE_PARAM_SECURE;
			}
			if (defined('SESSION_COOKIE_PARAM_HTTPONLY') && SESSION_COOKIE_PARAM_HTTPONLY) {
				$options['cookie_path'] = SESSION_COOKIE_PARAM_HTTPONLY;
			}
			$storage->setOptions($options);
		}

		$session = new SymfonySession($storage);
		$session->setName(SESSION);

		static::testSessionFixation($session);
		return $session;
	}

	protected static function testSessionFixation(SymfonySession $session) {
		$ip = $session->get('CLIENT_REMOTE_ADDR');
		$agent = $session->get('CLIENT_HTTP_USER_AGENT');
		if ($ip && $ip != $_SERVER['REMOTE_ADDR'] || $agent && $agent != $_SERVER['HTTP_USER_AGENT']) {
			$session->invalidate();
		}

		if (!$ip && isset($_SERVER['REMOTE_ADDR'])) {
			$session->set('CLIENT_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
		}
		if (!$agent && isset($_SERVER['HTTP_USER_AGENT'])) {
			$session->set('CLIENT_HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
		}
	}
}
<?

defined('C5_EXECUTE') or die(_("Access Denied."));

if (REDIRECT_TO_BASE_URL == true && (BASE_URL != 'http://' . $_SERVER['HTTP_HOST']) && (BASE_URL . ':' . $_SERVER['SERVER_PORT'] != 'http://' . $_SERVER['HTTP_HOST'])) {
	header('Location: ' . BASE_URL . $_SERVER['REQUEST_URI']);
	exit;
}

if (REDIRECT_TO_BASE_URL == true && strpos($_SERVER['REQUEST_URI'], '%7E') !== false) {
	header('Location: ' . BASE_URL . str_replace('%7E', '~', $_SERVER['REQUEST_URI']));
	exit;
}
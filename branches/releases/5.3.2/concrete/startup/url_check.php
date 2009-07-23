<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

if (REDIRECT_TO_BASE_URL == true && (BASE_URL != 'http://' . $_SERVER['HTTP_HOST']) && (BASE_URL . ':' . $_SERVER['SERVER_PORT'] != 'http://' . $_SERVER['HTTP_HOST'])) {
	header('Location: ' . BASE_URL . $_SERVER['REQUEST_URI']);
	exit;
}

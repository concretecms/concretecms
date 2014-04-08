<?
defined('C5_EXECUTE') or die("Access Denied.");

$l = Router::getInstance();
$l->setThemeByRoute('/dashboard', 'dashboard');
$l->setThemeByRoute('/dashboard/*', 'dashboard');
$l->setThemeByRoute('/account', VIEW_CORE_THEME);
$l->setThemeByRoute('/account/*', VIEW_CORE_THEME);

$l->setThemeByRoute('/page_forbidden', VIEW_CORE_THEME);
$l->setThemeByRoute('/page_not_found', VIEW_CORE_THEME);
$l->setThemeByRoute('/install', VIEW_CORE_THEME);
$l->setThemeByRoute('/login', VIEW_CORE_THEME);
$l->setThemeByRoute('/register', VIEW_CORE_THEME);
$l->setThemeByRoute('/maintenance_mode', VIEW_CORE_THEME);
$l->setThemeByRoute('/upgrade', VIEW_CORE_THEME);
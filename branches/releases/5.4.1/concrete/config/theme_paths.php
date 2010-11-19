<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$v = View::getInstance();
// TODO - make this honor * better, actually work for more than just dashboard
$v->setThemeByPath('/dashboard', 'dashboard');
$v->setThemeByPath('/dashboard/*', 'dashboard');

$v->setThemeByPath('/page_forbidden', VIEW_CORE_THEME);
$v->setThemeByPath('/page_not_found', VIEW_CORE_THEME);
$v->setThemeByPath('/install', VIEW_CORE_THEME);
$v->setThemeByPath('/login', VIEW_CORE_THEME);
$v->setThemeByPath('/register', VIEW_CORE_THEME);
$v->setThemeByPath('/maintenance_mode', VIEW_CORE_THEME);
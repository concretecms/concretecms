<?php defined('C5_EXECUTE') or die("Access Denied.");

Route::setThemeByRoute('/dashboard', 'dashboard');
Route::setThemeByRoute('/dashboard/*', 'dashboard');
Route::setThemeByRoute('/account', VIEW_CORE_THEME);
Route::setThemeByRoute('/account/*', VIEW_CORE_THEME);

Route::setThemeByRoute('/install', VIEW_CORE_THEME);
Route::setThemeByRoute('/login', VIEW_CORE_THEME);
Route::setThemeByRoute('/register', VIEW_CORE_THEME);
Route::setThemeByRoute('/maintenance_mode', VIEW_CORE_THEME);
Route::setThemeByRoute('/upgrade', VIEW_CORE_THEME);
